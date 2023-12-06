<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/ecs.php',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->rules([
        NoUnusedImportsFixer::class,
        StrictComparisonFixer::class,
        YodaStyleFixer::class,
        OrderedClassElementsFixer::class,
        NativeFunctionInvocationFixer::class,
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::ARRAY,
        SetList::SPACES,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::COMMENTS,
    ]);
};
