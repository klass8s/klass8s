<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac\Configurator\Binding;

enum SubjectKind
{
    case User;
    case Group;
    case ServiceAccount;
}
