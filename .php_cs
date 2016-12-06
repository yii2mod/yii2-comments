<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in([__DIR__]);

$config = PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        'phpdoc_align' => false,
        'phpdoc_summary' => false,
        'phpdoc_inline_tag' => false,
        'pre_increment' => false,
        'heredoc_to_nowdoc' => false,
        'cast_spaces' => false,
        'include' => false,
        'phpdoc_no_package' => false,
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => true,
        'single_quote' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_trailing_whitespace' => true,
        'no_trailing_whitespace_in_comment' => true,
        'phpdoc_order' => true,
        'trim_array_spaces' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);

return $config;
