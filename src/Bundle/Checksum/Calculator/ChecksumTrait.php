<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\Calculator;

use Klass8s\Bundle\Util\SortUtil;
use K8Schema\Api\Core\V1\ConfigMap;
use K8Schema\Api\Core\V1\Secret;

trait ChecksumTrait
{
    /**
     * @param Secret[]|ConfigMap[] $sources
     */
    private function checksum(array $sources): string
    {
        $mappedByNameAndKind = [];
        foreach ($sources as $source) {
            $data = $this->renderer->renderAsArray($source);
            SortUtil::ksortRecursive($data);
            $json = json_encode($data);

            $key = sprintf('%s:%s', $source->metadata()->getName(), $source::KIND);
            $mappedByNameAndKind[$key] = $json;
        }

        ksort($mappedByNameAndKind);

        return md5(implode(PHP_EOL, $mappedByNameAndKind));
    }
}
