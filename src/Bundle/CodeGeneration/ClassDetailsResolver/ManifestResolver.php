<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ClassDetailsResolver;

use Klass8s\Bundle\CodeGeneration\ClassDetails;
use Klass8s\App\AppInterface;
use Klass8s\Util\Str;

readonly class ManifestResolver
{
    /**
     * @param bool $useDedicatedDir Whether to create a dir for this manifest (like dir "Example" for "ExampleDeployment")
     */
    public function getClassDetails(AppInterface $app, string $manifestName, string $suffix, bool $useDedicatedDir = false): ClassDetails
    {
        $className = Str::withSuffix(Str::asClassName($manifestName), $suffix);
        $namespace = Str::asNamespace($app).'\\Manifest';
        $dir = Str::asDir($app).DIRECTORY_SEPARATOR.'Manifest';
        $fileName = $className.'.php';

        if ($useDedicatedDir) {
            $dirName = Str::asDirName($manifestName, $suffix);
            $namespace .= '\\'.$dirName;
            $dir .= DIRECTORY_SEPARATOR.$dirName;
        }

        return new ClassDetails($className, $namespace, $dir, $fileName);
    }
}
