<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum;

use Klass8s\Bundle\Checksum\Calculator\ChecksumCalculatorInterface;
use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\Job;

class ChecksumsService
{
    /**
     * @var ChecksumCalculatorInterface[]
     */
    private iterable $calculators;

    public function __construct(private readonly AnnotationSetter $annotationSetter, iterable $calculators)
    {
        $this->calculators = $calculators;
    }

    public function calculateChecksums(Deployment|Job|CronJob|StatefulSet $workload): void
    {
        foreach ($this->calculators as $calculator) {
            $annotation = $calculator->calculate($workload);
            $this->annotationSetter->setAnnotation($workload, $annotation);
        }
    }
}
