<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
readonly class BeforeMethod extends AbstractMethodDecoratorAttribute
{
}
