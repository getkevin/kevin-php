<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude(['vendor']);

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@PSR12' => true,
        '@Symfony' => true,
        '@PSR12:risky' => true,
        '@Symfony:risky' => true,
        'visibility_required' => false,
        'no_homoglyph_names' => false,
        'psr_autoloading' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'native_function_invocation' => false,
    ]
)->setFinder($finder);
