<?php

$finder = Symfony\CS\Finder::create()
    ->exclude('vendor')
    ->in([__DIR__]);

$config = Symfony\CS\Config::create()
    ->fixers([
        '-phpdoc_params',
        '-phpdoc_short_description',
        '-pre_increment',
        '-spaces_cast',
        '-include',
        '-phpdoc_no_package',
        'concat_with_spaces',
        'ordered_use',
    ])
    ->finder($finder);

return $config;
