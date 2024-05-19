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
    '@PhpCsFixer' => true,
    '@PER-CS:risky' => true,
    'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
    'blank_line_before_statement' => ['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'phpdoc', 'return', 'switch', 'throw', 'try', 'yield', 'yield_from']],
    'concat_space' => ['spacing' => 'one'],
    'doctrine_annotation_indentation' => true,
    'modernize_strpos' => true,
    'multiline_string_to_heredoc' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'numeric_literal_separator' => ['strategy' => 'use_separator'],
    'header_comment' => ['header' => <<<'EOF'
        This file is part of Web Push Notification Agent plugin for Unraid.

        (c) Peuuuur Noel

        This source file is subject to the MIT license that is bundled
        with this source code in the file LICENSE.
        EOF],
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
