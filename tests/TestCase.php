<?php

namespace JustBetter\EntryTranslator\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use JustBetter\EntryTranslator\ServiceProvider;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class TestCase extends AddonTestCase
{
    use LazilyRefreshDatabase, PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('statamic.editions.pro', true);
    }

    protected function getEnvironmentSetUp($app): void
    {
        Site::setSites([
            'en' => ['name' => 'English', 'locale' => 'en', 'url' => 'http://localhost/', 'default' => true],
            'nl' => ['name' => 'Dutch', 'locale' => 'nl', 'url' => 'http://localhost/nl', 'default' => false],
            'pt' => ['name' => 'Dutch', 'locale' => 'pt', 'url' => 'http://localhost/nl', 'default' => false],
        ]);

        Blueprint::make()
            ->setHandle('pages')
            ->setNamespace('collections.pages')
            ->setContents([
                'title' => 'Pages',
                'sections' => [
                    'main' => [
                        'display' => 'Main',
                        'fields' => [
                            [
                                'handle' => 'title',
                                'field' => [
                                    'type' => 'text',
                                    'display' => 'Title',
                                    'validate' => ['required'],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->save();

        parent::getEnvironmentSetUp($app);
    }
}
