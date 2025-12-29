<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress\Configurator;

use K8Schema\Api\Networking\V1\IngressRule;
use K8Schema\Api\Networking\V1\IngressRuleList;
use Klass8s\App\AppInterface;
use Klass8s\Registry\AppRegistry;
use Klass8s\Util\ManifestReferencesService;

class IngressRulesConfigurator
{
    /**
     * @var IngressRule[]
     */
    private array $hostsMap = [];

    private IngressRule|null $ruleWithoutHost = null;

    public function __construct(
        private readonly IngressRuleList $rules,
        private readonly AppInterface $app,
        private readonly ManifestReferencesService $referencesService,
        private readonly AppRegistry $appRegistry,
    ) {
    }

    public function addHttpRule(?string $host = null): IngressRuleConfigurator
    {
        $rule = null === $host ? $this->getRuleWithoutHost() : $this->getRuleForHost($host);

        return new IngressRuleConfigurator(
            rule: $rule,
            app: $this->app,
            referencesService: $this->referencesService,
            appRegistry: $this->appRegistry
        );
    }

    private function getRuleForHost(string $host): IngressRule
    {
        if (!array_key_exists($host, $this->hostsMap)) {
            $this->hostsMap[$host] = new IngressRule();
            $this->hostsMap[$host]->setHost($host);
            $this->rules->add($this->hostsMap[$host]);
        }

        return $this->hostsMap[$host];
    }

    private function getRuleWithoutHost(): IngressRule
    {
        if (null === $this->ruleWithoutHost) {
            $this->ruleWithoutHost = new IngressRule();
            $this->rules->add($this->ruleWithoutHost);
        }

        return $this->ruleWithoutHost;
    }
}
