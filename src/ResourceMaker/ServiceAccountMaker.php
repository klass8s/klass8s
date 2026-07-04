<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\ServiceAccount;
use K8Schema\APIResourceInterface;
use Klass8s\Core\ServiceAccount\Configurator\ImagePullSecretsConfigurator;
use Klass8s\Core\ServiceAccount\Configurator\SecretsConfigurator;
use Klass8s\Core\ServiceAccount\ServiceAccountInterface;
use Klass8s\Event\ServiceAccountGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;

class ServiceAccountMaker extends AbstractResourceMaker
{
    public function __construct(private readonly ManifestClassReferenceConverter $referencesService)
    {
    }

    protected function supportedClass(): string
    {
        return ServiceAccountInterface::class;
    }

    protected function makeResource(ManifestInterface|ServiceAccountInterface $manifest): APIResourceInterface
    {
        $serviceAccount = new ServiceAccount();
        $app = $manifest->app;
        $app::configureMeta($manifest, $serviceAccount);

        $imagePullSecrets = new ImagePullSecretsConfigurator($this->referencesService, $serviceAccount->imagePullSecrets());
        $secrets = new SecretsConfigurator($this->referencesService, $serviceAccount->secrets());

        $manifest->imagePullSecrets($imagePullSecrets);
        $manifest->secrets($secrets);

        if (null !== ($automount = $manifest->automountServiceAccountToken())) {
            $serviceAccount->setAutomountServiceAccountToken($automount);
        }

        $manifest->configureServiceAccount($serviceAccount);

        $this->dispatcher->dispatch(
            new ServiceAccountGeneratedEvent($manifest, $serviceAccount, $app),
            ServiceAccountGeneratedEvent::NAME
        );

        return $serviceAccount;
    }
}
