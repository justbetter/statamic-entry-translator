<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use DeepL\DeepLClient;
use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Actions\TranslateEntry;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
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

class TranslateEntryTest extends TestCase
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
        $action = app(TranslateEntry::class);
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

        $action->translate($entry, $site);
    }

    #[Test]
    public function it_creates_a_localization_when_the_target_entry_does_not_exist(): void
    {
        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        $this->mock(DeepLClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateText')
                ->once()
                ->with(['foo'], 'en-US', 'nl')
                ->andReturn([(object) ['text' => 'bar']]);
        });

        $entry = $this->setupData();
        /** @var StatamicSite $site */
        $site = Site::get('nl');

        app(TranslateEntry::class)->translate($entry, $site);

        $localized = $entry->in('nl');
        $this->assertInstanceOf(Entry::class, $localized);
        $this->assertSame('bar', $localized->get('title'));
    }
}
