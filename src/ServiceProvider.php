<?php

namespace JustBetter\EntryTranslator;

use JustBetter\EntryTranslator\Actions\Fields\CollectLocalisableFields;
use JustBetter\EntryTranslator\Actions\ResolveTranslator;
use JustBetter\EntryTranslator\Actions\TranslateEntries;
use JustBetter\EntryTranslator\Actions\TranslateEntry;
use JustBetter\EntryTranslator\Translators\DeeplTranslator;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->bootConfig();
    }

    public function register(): void
    {
        parent::register();

        $this->registerConfig()
            ->registerActions();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-entry-translator.php', 'justbetter.statamic-entry-translator');

        return $this;
    }

    protected function registerActions(): static
    {
        TranslateEntries::bind();
        TranslateEntry::bind();

        CollectLocalisableFields::bind();

        ResolveTranslator::bind();

        DeeplTranslator::bind();

        return $this;
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/statamic-entry-translator.php' => config_path('justbetter/statamic-entry-translator.php'),
        ], 'justbetter-statamic-entry-translator');

        return $this;
    }
}
