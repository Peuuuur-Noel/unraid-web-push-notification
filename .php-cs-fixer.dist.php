<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PER' => true,
    '@PER-CS:risky' => true,
    'header_comment' => ['header' => <<<'EOF'
        This file is part of Web Push Notification Agent plugin for Unraid.

        (c) Peuuuur Noel

        This source file is subject to the MIT license that is bundled
        with this source code in the file LICENSE.
        EOF],
    // 'declare_strict_types' => true,
    'ordered_types' => ['null_adjustment' => 'always_first', 'sort_algorithm' => 'alpha'],
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
    'modernize_strpos' => true,
    'no_useless_concat_operator' => false,
    'numeric_literal_separator' => ['strategy' => 'use_separator'],
    'method_chaining_indentation' => true,
    'phpdoc_indent' => true,
    'doctrine_annotation_indentation' => true,
    'explicit_string_variable' => true,
    'heredoc_to_nowdoc' => true,
    'multiline_string_to_heredoc' => true,
    'simple_to_complex_string_variable' => true,
    'array_indentation' => true,
];

$finder = (new Finder())
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->in([
        __DIR__ . '/src',
    ])
    ->append([__FILE__]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder);
