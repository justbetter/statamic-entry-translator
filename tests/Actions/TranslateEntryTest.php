<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

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
    protected function setUpData(): Entry
    {
        $collection = Collection::make('pages');
        $collection->save();

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
                                'import' => 'seo',
                            ],
                        ],
                    ],
                ],
            ])
            ->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        $entry = $entry->collection($collection);
        $entry = $entry->data(['title' => 'foo']);
        $entry->save();

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
}
