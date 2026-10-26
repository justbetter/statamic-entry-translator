<?php

namespace JustBetter\EntryTranslator\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JustBetter\EntryTranslator\ServiceProvider;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy as TaxonomyFacade;
use Statamic\Facades\Term as TermFacade;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Taxonomies\LocalizedTerm;
use Statamic\Taxonomies\Taxonomy;
use Statamic\Taxonomies\Term;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class TestCase extends AddonTestCase
{
    use PreventsSavingStacheItemsToDisk, RefreshDatabase;

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

        $app['config']->set('app.key', 'base64:7tG0yY7g3QkFrQ+Vk4EBSbcT8D9C4/5Dph1dNRjh6WU=');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        return [
            ...parent::getPackageProviders($app),
            StatamicServiceProvider::class,
        ];
    }

    /**
     * @param  array<int, string>  $sites
     * @param  array<string, mixed>  $data
     */
    protected function makeTerm(array $sites = ['en', 'nl', 'pt'], array $data = []): LocalizedTerm
    {
        // Taxonomies only respect their configured sites when multisite is enabled.
        config()->set('statamic.system.multisite', true);

        /** @var Taxonomy $taxonomy */
        $taxonomy = TaxonomyFacade::make('tags');
        $taxonomy->sites($sites)->saveQuietly();

        /** @var Term $term */
        $term = TermFacade::make('hello');
        $term->taxonomy('tags')->dataForLocale('en', $data);

        /** @var LocalizedTerm $localizedTerm */
        $localizedTerm = $term->in('en');

        return $localizedTerm;
    }
}
