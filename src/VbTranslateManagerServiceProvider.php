<?php

namespace EkremOgul\VbTranslateManager;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use EkremOgul\VbTranslateManager\Commands\VbTranslateManagerCommand;

class VbTranslateManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('vb-translate-manager')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_vb_translate_manager_table')
            ->hasCommand(VbTranslateManagerCommand::class);
    }
}
