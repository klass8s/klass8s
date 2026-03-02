<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway;

use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface GatewayInterface extends ManifestInterface
{
    public function gatewayClassName(): string;

    public function listeners(ListenersConfigurator $listeners): void;

    /**
     * @return list<array{type: string, value: string}>
     */
    public function addresses(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function infrastructure(): ?array;
}
