<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration;

final readonly class TemplateRenderer
{
    public function __construct(private string $templatesDir)
    {
    }

    public function render(string $templateName, array $variables): false|string
    {
        ob_get_clean();
        ob_start();
        extract($variables);
        $tplPath = $this->templatesDir.DIRECTORY_SEPARATOR.$templateName;
        if (!file_exists($tplPath)) {
            throw new \InvalidArgumentException(sprintf('Template "%s" does not exists', $templateName));
        }
        require $tplPath;

        return ob_get_clean();
    }
}
