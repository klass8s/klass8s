<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\PodTemplateGetter;

use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\Job;
use K8Schema\Api\Core\V1\PodTemplateSpec;

class DefaultPodTemplateGetter
{
    public function get(Deployment|Job|StatefulSet $workload): PodTemplateSpec
    {
        return $workload->spec()->template();
    }
}
