<?php

$finder = Symfony\CS\Finder::create()
    ->exclude('vendor')
    ->in([__DIR__]);

$config = Symfony\CS\Config::create()
    ->fixers([
        '-phpdoc_params',
        '-phpdoc_short_description',
        '-phpdoc_inline_tag',
        '-pre_increment',
        '-heredoc_to_nowdoc',
        '-spaces_cast',
        '-include',
        '-phpdoc_no_package',
        'concat_with_spaces',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder);

return $config;
