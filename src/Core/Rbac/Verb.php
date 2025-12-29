<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

enum Verb: string
{
    case Get = 'get';
    case List = 'list';
    case Watch = 'watch';
    case Create = 'create';
    case Update = 'update';
    case Patch = 'patch';
    case Delete = 'delete';
}
