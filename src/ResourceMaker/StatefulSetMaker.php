<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Apps\V1\StatefulSetSpec;
use K8Schema\Api\Apps\V1\StatefulSetUpdateStrategy;
use K8Schema\Api\Core\V1\PersistentVolumeClaim;
use K8Schema\APIResourceInterface;
use K8Schema\K8SApi;
use Klass8s\Core\Pod\PodTemplateSpecProcessor;
use Klass8s\Core\Service\ServiceInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;
use Klass8s\Core\StatefulSet\UpdateStrategy\UpdateStrategyConfigurator;
use Klass8s\Event\StatefulSetGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyFactory;
use Klass8s\Registry\AppRegistry;
use Klass8s\ResourceMaker\Traits\ConfigureSelectorTrait;

class StatefulSetMaker extends AbstractResourceMaker
{
    use ConfigureSelectorTrait;

    public function __construct(
        private readonly AppRegistry $appRegistry,
        private readonly PersistentVolumeClaimMaker $pvcMaker,
        private readonly PodTemplateSpecProcessor $podSpecProcessor,
        private readonly ProxyFactory $proxyFactory
    ) {
    }

    protected function supportedClass(): string
    {
        return StatefulSetInterface::class;
    }

    protected function makeResource(ManifestInterface|StatefulSetInterface $manifest): APIResourceInterface
    {
        $serviceReference = $manifest->serviceName();
        $serviceOwningApp = $this->appRegistry->get($serviceReference->appAlias);
        $serviceName = $serviceOwningApp->resourceNameFromManifestClass($serviceReference->className, ServiceInterface::class);

        if (K8SApi::versionIsHigherThanOrEqualTo('v1.33.1')) {
            $sts = new StatefulSet();
            $spec = $sts->spec();
            $spec->setServiceName($serviceName);
        } else {
            $spec = new StatefulSetSpec($serviceName);
            $sts = new StatefulSet($spec);
        }

        $this->configureSelector($manifest, $spec->selector());
        $app = $manifest->app;
        $app::configureMeta($manifest, $sts);
        $this->podSpecProcessor->process($manifest, $spec->template(), $app);

        foreach ($spec->selector()->matchLabels()->all() as $name => $value) {
            $sts->metadata()->labels()->add($name, $value);
            $spec->template()->metadata()->labels()->add($name, $value);
        }

        $podManagementPolicy = $manifest->podManagementPolicy();
        if (null !== $podManagementPolicy) {
            $spec->setPodManagementPolicy($podManagementPolicy->toString());
        }

        $spec
            ->setReplicas($manifest->replicas())
            ->setRevisionHistoryLimit($manifest->revisionHistoryLimit());

        $specUpdateStrategy = $spec->getUpdateStrategy() ?? new StatefulSetUpdateStrategy();
        $updateStrategy = new UpdateStrategyConfigurator($specUpdateStrategy);
        $manifest->updateStrategy($updateStrategy);
        if ([] !== array_filter($specUpdateStrategy->jsonSerialize()->getArrayCopy())) {
            $spec->setUpdateStrategy($specUpdateStrategy);
        }

        foreach ($manifest->volumeClaimTemplates() as $template) {
            $template->setApp($app);
            if (!str_contains($template::class, '@anonymous')) {
                $template = $this->proxyFactory->makeProxy($template);
            }
            /** @var PersistentVolumeClaim $pvc */
            $pvc = $this->pvcMaker->make($template);
            $pvc->metadata()->setName($template::shortName()); // Rewrite PVC full name to it's short name
            $spec->volumeClaimTemplates()->add($pvc);
        }

        $manifest->configureStatefulSet($sts);

        $this->dispatcher->dispatch(new StatefulSetGeneratedEvent($manifest, $sts, $app), StatefulSetGeneratedEvent::NAME);

        return $sts;
    }
}
