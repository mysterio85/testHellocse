<?php

use PhpCsFixer\Config;
use PhpCsFixer\Fixer\Import\ImportNamespaceFixer;
use PhpCsFixer\Fixer\Naming\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Spacing\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'single_quote' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor','storage'])
    );
