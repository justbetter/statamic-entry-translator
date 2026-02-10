<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Facades\TranslateEntry;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Fieldset;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateEntryFacadeTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        Site::setSites([
            'en' => ['name' => 'English', 'locale' => 'en', 'url' => 'http://localhost/', 'default' => true],
            'nl' => ['name' => 'Dutch', 'locale' => 'nl', 'url' => 'http://localhost/nl', 'default' => false],
            'pt' => ['name' => 'Dutch', 'locale' => 'pt', 'url' => 'http://localhost/nl', 'default' => false],
        ]);

        Fieldset::make('seo')
            ->setContents([
                'title' => 'SEO',
                'handle' => 'seo',
                'fields' => [
                    [
                        'handle' => 'description',
                        'field' => [
                            'type' => 'textarea',
                            'display' => 'Description',
                            'instructions' => 'Meta description',
                            'character_limit' => 160,
                        ],
                    ],
                ],
            ])
            ->save();

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
                            [
                                'field' => [
                                    'import' => 'seo',
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->save();

        parent::getEnvironmentSetUp($app);
    }

    protected function setUpData(): Entry
    {
        $collection = Collection::make('pages');
        $collection->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        $entry = $entry->collection($collection);
        $entry = $entry->data(['title' => 'foo']);
        $entry->saveQuietly();

        return $entry;
    }

    #[Test]
    public function it_can_call_a_translator(): void
    {
        $resolver = app(ResolvesTranslator::class);
        $translator = $resolver->resolve();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = Site::all();
        $entry = $this->setupData();
        /** @var StatamicSite $site */
        $site = $sites->first();

        $this->mock($translator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translate')
                ->once();
        });

        TranslateEntry::translate($entry, $site);
    }
}
