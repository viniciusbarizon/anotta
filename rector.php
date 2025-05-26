<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelLevelSetList;

use function Pest\Laravel\instance;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/lang',
        __DIR__.'/public',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ])
    ->withPreparedSets(
        carbon: true,
        codeQuality: true,
        codingStyle: true,
        deadCode: true,
        earlyReturn: true,
        instanceOf: true,
        naming: false,
        privatization: false,
        rectorPreset: true,
        strictBooleans: true,
        typeDeclarations: true,
    );
