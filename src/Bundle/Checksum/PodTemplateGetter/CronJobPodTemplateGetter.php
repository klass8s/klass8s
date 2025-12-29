<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\PodTemplateGetter;

use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Core\V1\PodTemplateSpec;

class CronJobPodTemplateGetter
{
    public function get(CronJob $workload): PodTemplateSpec
    {
        return $workload->getSpec()->jobTemplate()->spec()->template();
    }
}
