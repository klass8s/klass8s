<?php

declare(strict_types=1);

// php-cs-fixer runs on the PHP that phn compiles from src/ and tests/ (.src/ and .tests/).
// `composer cs` is `phn fmt`, which carries every change back to the .phn it came from.
$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/.src', __DIR__ . '/.tests'])
    ->append([__FILE__]);

$config = new PhpCsFixer\Config();

return $config
    ->setRules(
        [
            '@PHP8x0Migration' => true,
            '@PHP8x0Migration:risky' => true,
            '@PHP8x1Migration' => true,
            '@PHP8x2Migration' => true,
            '@PER-CS3x0' => true,
            'protected_to_private' => false,
            'modifier_keywords' => [
                'elements' => ['const', 'method'], // omitted 'property' to not have `public` before `protected(set)`
            ],
        ],
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
