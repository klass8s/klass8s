<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\PodTemplateGetter;

use Composer\Semver\Comparator;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Core\V1\PodTemplateSpec;
use K8Schema\K8SApi;

class StatefulSetPodTemplateGetter
{
    public function get(StatefulSet $workload): PodTemplateSpec
    {
        $spec = Comparator::greaterThanOrEqualTo(K8SApi::VERSION, 'v1.33.1')
            ? $workload->spec()
            : $workload->getSpec();

        return $spec->template();
    }
}
