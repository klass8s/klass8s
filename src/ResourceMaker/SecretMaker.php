<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\Secret;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Secret\SecretInterface;
use Klass8s\Event\SecretGeneratedEvent;
use Klass8s\ResourceMaker\Traits\PrefixMapKeysTrait;
use Klass8s\Util\StringMapProxy;

class SecretMaker extends AbstractResourceMaker
{
    use PrefixMapKeysTrait;

    public function makeResource(ManifestInterface|SecretInterface $manifest, AppInterface $app): Secret
    {
        $secret = new Secret();

        $app->metadataHelper()->configureMeta($manifest, $secret);
        $data = StringMapProxy::make($secret->data());
        $manifest->data($data);
        $stringData = StringMapProxy::make($secret->stringData());
        $manifest->stringData($stringData);

        if ($prefix = $manifest->keysPrefix()) {
            $this->prefixMapKeys($prefix, $data);
            $this->prefixMapKeys($prefix, $stringData);
        }

        foreach ($data->all() as $key => $value) {
            $data->add($key, base64_encode($value));
        }

        $secret->setType($manifest->type()->value);

        $manifest->configureSecret($secret);

        $this->dispatcher->dispatch(new SecretGeneratedEvent($manifest, $secret, $app), SecretGeneratedEvent::NAME);

        return $secret;
    }

    protected function supportsClass(): string
    {
        return SecretInterface::class;
    }
}
