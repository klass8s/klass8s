<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ClassDetailsResolver;

use Klass8s\Bundle\CodeGeneration\ClassDetails;
use Klass8s\Util\StringUtil;

readonly class AppResolver
{
    private const string SUFFIX_APP = 'App';

    private string $namespacePrefix;

    public function __construct(private string $codeDir, string $namespacePrefix)
    {
        $this->namespacePrefix = trim($namespacePrefix, '\\');
    }

    public function getClassDetails(string $appName): ClassDetails
    {
        $className = $this->getClassName($appName);
        $namespace = $this->getNamespace($appName);
        $dir = $this->getDir($appName);
        $fileName = $className.'.php';

        return new ClassDetails($className, $namespace, $dir, $fileName);
    }

    private function getClassName(string $appName): string
    {
        $className = StringUtil::asClassName($appName);

        return StringUtil::withSuffix($className, self::SUFFIX_APP);
    }

    private function getNamespace(string $appName): string
    {
        $dirName = StringUtil::asDirName($appName, self::SUFFIX_APP);

        return $this->namespacePrefix.'\\'.$dirName;
    }

    private function getDir(string $appName): string
    {
        $dirName = StringUtil::asDirName($appName, self::SUFFIX_APP);

        return $this->codeDir.DIRECTORY_SEPARATOR.$dirName;
    }
}
