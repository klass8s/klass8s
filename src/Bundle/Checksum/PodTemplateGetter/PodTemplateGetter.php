<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\PodTemplateGetter;

use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\Job;
use K8Schema\Api\Core\V1\PodTemplateSpec;

class PodTemplateGetter
{
    private array $kindsMap;

    public function __construct(
        DefaultPodTemplateGetter $defaultGetter,
        CronJobPodTemplateGetter $cronJobGetter,
        StatefulSetPodTemplateGetter $stsGetter
    ) {
        $this->kindsMap = [
            Deployment::KIND => $defaultGetter,
            Job::KIND => $defaultGetter,
            CronJob::KIND => $cronJobGetter,
            StatefulSet::KIND => $stsGetter,
        ];
    }

    public function get(Deployment|Job|CronJob|StatefulSet $workload): PodTemplateSpec
    {
        return $this->kindsMap[$workload::KIND]->get($workload);
    }
}
