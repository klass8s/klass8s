<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart;

enum ChartReferenceType
{
    case RelativePath;
    case AbsolutePath;
    case ChartName;
    case Url;
    case OciRegistry;
}
