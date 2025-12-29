<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator;

use Klass8s\Bundle\CodeGeneration\ClassDetails;
use Klass8s\Bundle\CodeGeneration\ClassDetailsResolver\ManifestResolver;
use Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context\ContextInterface;
use Klass8s\Bundle\CodeGeneration\TemplateRenderer;
use Klass8s\Bundle\Util\Dir;
use Klass8s\App\AppInterface;

final readonly class ManifestGenerator
{
    public function __construct(
        private ManifestResolver $resolver,
        private TemplateRenderer $renderer,
    ) {
    }

    public function generate(string $manifestName, ContextInterface $context, AppInterface $app): string
    {
        $details = $this->resolver->getClassDetails(
            $app,
            $manifestName,
            $context->kind(),
            $context->createDedicatedDir()
        );
        Dir::create($details->directory);
        $code = $this->generateCode($details, $manifestName, $context);
        $fileName = $details->fullFilePath();
        file_put_contents($fileName, $code);

        return $fileName;
    }

    protected function generateCode(ClassDetails $details, string $manifestName, ContextInterface $context): string
    {
        return $this->renderer->render(
            $context->templateName(),
            $context->templateVariables($details, $manifestName)
        );
    }
}
