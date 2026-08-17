<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\PodTemplateGetter;

use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Core\V1\PodTemplateSpec;

class StatefulSetPodTemplateGetter
{
    public function get(StatefulSet $workload): PodTemplateSpec
    {
        // k8schema >= 1.33.1 exposes spec(); older generations getSpec(). Probe the
        // object rather than K8SApi::versionIsHigherThanOrEqualTo(): that helper
        // compares the un-normalized 'v1.34' constant and answers false.
        $spec = method_exists($workload, 'spec') ? $workload->spec() : $workload->getSpec();

        return $spec->template();
    }
}
