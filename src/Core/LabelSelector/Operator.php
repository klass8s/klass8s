<?php

declare(strict_types=1);

namespace Klass8s\Core\LabelSelector;

final class Operator
{
    public const string IN = 'In';
    public const string NOT_IN = 'NotIn';
    public const string EXISTS = 'Exists';
    public const string DOES_NOT_EXIST = 'DoesNotExist';
    public const string GREATER_THAN = 'Gt';
    public const string LOWER_THAN = 'Lt';
}
