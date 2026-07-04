<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi;

use Klass8s\App\AppProcessor;
use Klass8s\Renderer\YamlRenderer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class GatewayApiRenderTest extends KernelTestCase
{
    private AppProcessor $appProcessor;
    private YamlRenderer $renderer;

    protected static function getKernelClass(): string
    {
        return GatewayApiTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->appProcessor = self::getContainer()->get(AppProcessor::class);
        $this->renderer = new YamlRenderer();
    }

    public function testGatewayClassRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $gatewayClass = $this->findDocument($documents, 'GatewayClass');
        self::assertNotNull($gatewayClass, 'GatewayClass document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $gatewayClass['apiVersion']);
        self::assertSame('GatewayClass', $gatewayClass['kind']);
        self::assertSame('gateway-main', $gatewayClass['metadata']['name']);

        // Spec
        self::assertSame('example.io/gateway-controller', $gatewayClass['spec']['controllerName']);
        self::assertSame('Test gateway class', $gatewayClass['spec']['description']);

        // Default labels
        $this->assertDefaultLabels($gatewayClass, 'gateway', 'main');
        // Klass8s annotations
        $this->assertKlass8sAnnotations($gatewayClass, 'gateway');
    }

    public function testGatewayRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $gateway = $this->findDocument($documents, 'Gateway');
        self::assertNotNull($gateway, 'Gateway document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $gateway['apiVersion']);
        self::assertSame('Gateway', $gateway['kind']);
        self::assertSame('gateway-main', $gateway['metadata']['name']);

        // Spec
        self::assertSame('gateway-main', $gateway['spec']['gatewayClassName']);

        // Listeners
        self::assertCount(2, $gateway['spec']['listeners']);

        $httpListener = $gateway['spec']['listeners'][0];
        self::assertSame('http', $httpListener['name']);
        self::assertSame(80, $httpListener['port']);
        self::assertSame('HTTP', $httpListener['protocol']);
        self::assertSame('*.example.com', $httpListener['hostname']);
        self::assertSame('Same', $httpListener['allowedRoutes']['namespaces']['from']);

        $httpsListener = $gateway['spec']['listeners'][1];
        self::assertSame('https', $httpsListener['name']);
        self::assertSame(443, $httpsListener['port']);
        self::assertSame('HTTPS', $httpsListener['protocol']);
        self::assertSame('Terminate', $httpsListener['tls']['mode']);
        self::assertSame('wildcard-cert', $httpsListener['tls']['certificateRefs'][0]['name']);

        // Addresses
        self::assertCount(1, $gateway['spec']['addresses']);
        self::assertSame('IPAddress', $gateway['spec']['addresses'][0]['type']);
        self::assertSame('10.0.0.1', $gateway['spec']['addresses'][0]['value']);

        $this->assertDefaultLabels($gateway, 'gateway', 'main');
        $this->assertKlass8sAnnotations($gateway, 'gateway');
    }

    public function testHTTPRouteRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $route = $this->findDocument($documents, 'HTTPRoute');
        self::assertNotNull($route, 'HTTPRoute document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $route['apiVersion']);
        self::assertSame('HTTPRoute', $route['kind']);
        self::assertSame('gateway-api', $route['metadata']['name']);

        // Parent refs
        self::assertCount(1, $route['spec']['parentRefs']);
        self::assertSame('gateway-main', $route['spec']['parentRefs'][0]['name']);
        self::assertSame('https', $route['spec']['parentRefs'][0]['sectionName']);

        // Hostnames
        self::assertSame(['api.example.com'], $route['spec']['hostnames']);

        // Rules
        self::assertCount(2, $route['spec']['rules']);

        // Rule 1: api-v1
        $rule1 = $route['spec']['rules'][0];
        self::assertSame('api-v1', $rule1['name']);
        self::assertCount(2, $rule1['matches']);
        self::assertSame('PathPrefix', $rule1['matches'][0]['path']['type']);
        self::assertSame('/api/v1', $rule1['matches'][0]['path']['value']);
        self::assertSame('GET', $rule1['matches'][0]['method']);
        self::assertSame('POST', $rule1['matches'][1]['method']);

        // Backend refs with weights
        self::assertCount(2, $rule1['backendRefs']);
        self::assertSame('api-service', $rule1['backendRefs'][0]['name']);
        self::assertSame(8080, $rule1['backendRefs'][0]['port']);
        self::assertSame(90, $rule1['backendRefs'][0]['weight']);
        self::assertSame('api-canary', $rule1['backendRefs'][1]['name']);
        self::assertSame(10, $rule1['backendRefs'][1]['weight']);

        // Timeouts
        self::assertSame('30s', $rule1['timeouts']['request']);
        self::assertSame('10s', $rule1['timeouts']['backendRequest']);

        // Rule 2: redirect
        $rule2 = $route['spec']['rules'][1];
        self::assertSame('old-api-redirect', $rule2['name']);
        self::assertSame('RequestRedirect', $rule2['filters'][0]['type']);
        self::assertSame(301, $rule2['filters'][0]['requestRedirect']['statusCode']);

        $this->assertDefaultLabels($route, 'gateway', 'api');
        $this->assertKlass8sAnnotations($route, 'gateway');
    }

    public function testHttpsRedirectRouteRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $route = $this->findDocumentByName($documents, 'HTTPRoute', 'gateway-https-redirect');
        self::assertNotNull($route, 'https-redirect HTTPRoute document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $route['apiVersion']);

        // Attaches only to the Gateway's HTTP listener, cross-namespace
        self::assertCount(1, $route['spec']['parentRefs']);
        self::assertSame('gateway-main', $route['spec']['parentRefs'][0]['name']);
        self::assertSame('edge', $route['spec']['parentRefs'][0]['namespace']);
        self::assertSame('http', $route['spec']['parentRefs'][0]['sectionName']);

        // Catch-all: no hostnames restriction
        self::assertArrayNotHasKey('hostnames', $route['spec']);

        // Single rule: match everything, 301 to https
        self::assertCount(1, $route['spec']['rules']);
        $rule = $route['spec']['rules'][0];
        self::assertSame('PathPrefix', $rule['matches'][0]['path']['type']);
        self::assertSame('/', $rule['matches'][0]['path']['value']);
        self::assertSame('RequestRedirect', $rule['filters'][0]['type']);
        self::assertSame('https', $rule['filters'][0]['requestRedirect']['scheme']);
        self::assertSame(301, $rule['filters'][0]['requestRedirect']['statusCode']);

        $this->assertDefaultLabels($route, 'gateway', 'https-redirect');
        $this->assertKlass8sAnnotations($route, 'gateway');
    }

    public function testGRPCRouteRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $route = $this->findDocument($documents, 'GRPCRoute');
        self::assertNotNull($route, 'GRPCRoute document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $route['apiVersion']);
        self::assertSame('GRPCRoute', $route['kind']);
        self::assertSame('gateway-grpc', $route['metadata']['name']);

        self::assertSame(['grpc.example.com'], $route['spec']['hostnames']);

        // Rules
        self::assertCount(1, $route['spec']['rules']);
        $rule = $route['spec']['rules'][0];
        self::assertSame('user-service', $rule['name']);

        // GRPC method match
        self::assertSame('Exact', $rule['matches'][0]['method']['type']);
        self::assertSame('mypackage.UserService', $rule['matches'][0]['method']['service']);
        self::assertSame('GetUser', $rule['matches'][0]['method']['method']);

        // Backend ref
        self::assertSame('user-grpc', $rule['backendRefs'][0]['name']);
        self::assertSame(9090, $rule['backendRefs'][0]['port']);

        $this->assertDefaultLabels($route, 'gateway', 'grpc');
        $this->assertKlass8sAnnotations($route, 'gateway');
    }

    public function testReferenceGrantRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $grant = $this->findDocument($documents, 'ReferenceGrant');
        self::assertNotNull($grant, 'ReferenceGrant document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $grant['apiVersion']);
        self::assertSame('ReferenceGrant', $grant['kind']);
        self::assertSame('gateway-allow-routes', $grant['metadata']['name']);

        // From
        self::assertCount(2, $grant['spec']['from']);
        self::assertSame('HTTPRoute', $grant['spec']['from'][0]['kind']);
        self::assertSame('frontend-ns', $grant['spec']['from'][0]['namespace']);
        self::assertSame('Gateway', $grant['spec']['from'][1]['kind']);
        self::assertSame('infra-ns', $grant['spec']['from'][1]['namespace']);

        // To
        self::assertCount(2, $grant['spec']['to']);
        self::assertSame('Service', $grant['spec']['to'][0]['kind']);
        self::assertSame('Secret', $grant['spec']['to'][1]['kind']);
        self::assertSame('tls-cert', $grant['spec']['to'][1]['name']);

        $this->assertDefaultLabels($grant, 'gateway', 'allow-routes');
        $this->assertKlass8sAnnotations($grant, 'gateway');
    }

    public function testBackendTLSPolicyRendering(): void
    {
        $output = $this->appProcessor->process('gateway');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $policy = $this->findDocument($documents, 'BackendTLSPolicy');
        self::assertNotNull($policy, 'BackendTLSPolicy document not found in rendered output');

        self::assertSame('gateway.networking.k8s.io/v1', $policy['apiVersion']);
        self::assertSame('BackendTLSPolicy', $policy['kind']);
        self::assertSame('gateway-backend-tls', $policy['metadata']['name']);

        // Target refs
        self::assertCount(1, $policy['spec']['targetRefs']);
        self::assertSame('Service', $policy['spec']['targetRefs'][0]['kind']);
        self::assertSame('api-service', $policy['spec']['targetRefs'][0]['name']);
        self::assertSame('https', $policy['spec']['targetRefs'][0]['sectionName']);

        // Validation
        self::assertSame('api.internal', $policy['spec']['validation']['hostname']);
        self::assertSame('api-ca-cert', $policy['spec']['validation']['caCertificateRefs'][0]['name']);
        self::assertSame('Hostname', $policy['spec']['validation']['subjectAltNames'][0]['type']);
        self::assertSame('api.internal', $policy['spec']['validation']['subjectAltNames'][0]['hostname']);

        $this->assertDefaultLabels($policy, 'gateway', 'backend-tls');
        $this->assertKlass8sAnnotations($policy, 'gateway');
    }

    private function assertDefaultLabels(array $document, string $appName, string $componentName): void
    {
        $labels = $document['metadata']['labels'] ?? [];
        self::assertSame($appName, $labels['app.kubernetes.io/name'] ?? null, 'Missing or wrong app.kubernetes.io/name label');
        self::assertSame($componentName, $labels['app.kubernetes.io/component'] ?? null, 'Missing or wrong app.kubernetes.io/component label');
    }

    private function assertKlass8sAnnotations(array $document, string $appName): void
    {
        $annotations = $document['metadata']['annotations'] ?? [];
        self::assertSame($appName, $annotations['klass8s.dev/app'] ?? null, 'Missing or wrong klass8s.dev/app annotation');
        self::assertArrayHasKey('klass8s.dev/deploy-priority', $annotations, 'Missing klass8s.dev/deploy-priority annotation');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseYamlDocuments(string $yaml): array
    {
        $documents = [];
        // Split by YAML document separator, skip file path comments
        foreach (preg_split('/\n---\n/', $yaml) as $doc) {
            $doc = trim($doc);
            if ('' === $doc) {
                continue;
            }
            // Remove "# File: ..." header lines
            $doc = preg_replace('/^#\s*File:.*\n/', '', $doc);
            $parsed = Yaml::parse($doc);
            if (is_array($parsed) && isset($parsed['kind'])) {
                $documents[] = $parsed;
            }
        }

        return $documents;
    }

    private function findDocument(array $documents, string $kind): ?array
    {
        foreach ($documents as $doc) {
            if (($doc['kind'] ?? null) === $kind) {
                return $doc;
            }
        }

        return null;
    }

    private function findDocumentByName(array $documents, string $kind, string $name): ?array
    {
        foreach ($documents as $doc) {
            if (($doc['kind'] ?? null) === $kind && ($doc['metadata']['name'] ?? null) === $name) {
                return $doc;
            }
        }

        return null;
    }
}
