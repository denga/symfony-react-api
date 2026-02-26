<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/config',
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::COMMON,
        SetList::CLEAN_CODE,
        SetList::DOCBLOCK,
        SetList::SPACES,
        SetList::STRICT,
        SetList::PHPUNIT,
    ]);

    $ecsConfig->dynamicSets(['@Symfony']);

    // Skip folders
    $ecsConfig->skip([
        __DIR__ . '/config/reference.php',
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        __DIR__ . '/public/build',
    ]);

    $ecsConfig->rule(DeclareStrictTypesFixer::class);
    $ecsConfig->rule(NoUnusedImportsFixer::class);
    $ecsConfig->rule(NoSuperfluousPhpdocTagsFixer::class);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->ruleWithConfiguration(NoExtraBlankLinesFixer::class, [
        'tokens' => ['extra', 'throw', 'use'],
    ]);

    $ecsConfig->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, [
        'default' => 'single_space',
    ]);

    $ecsConfig->ruleWithConfiguration(FullyQualifiedStrictTypesFixer::class, [
        'import_symbols' => true,
        'leading_backslash_in_global_namespace' => true,
    ]);

    $ecsConfig->ruleWithConfiguration(OrderedClassElementsFixer::class, [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'method_public',
            'method_protected',
            'method_private',
        ],
    ]);

    $ecsConfig->cacheDirectory(__DIR__ . '/var/ecs_cache');
};
