<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\Calculator;

use Klass8s\Bundle\Checksum\ChecksumAnnotation;
use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\Job;

interface ChecksumCalculatorInterface
{
    public function calculate(Deployment|Job|CronJob|StatefulSet $workload): ChecksumAnnotation;
}
