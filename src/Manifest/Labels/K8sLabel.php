<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Labels;

enum K8sLabel: string
{
    case Name = 'app.kubernetes.io/name';
    case Instance = 'app.kubernetes.io/instance';
    case Component = 'app.kubernetes.io/component';
    case PartOf = 'app.kubernetes.io/part-of';
    case ManagedBy = 'app.kubernetes.io/managed-by';
    case Version = 'app.kubernetes.io/version';
}
