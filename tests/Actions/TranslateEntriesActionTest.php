<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Actions\TranslateEntriesAction;
use JustBetter\EntryTranslator\Jobs\TranslateEntriesJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;

class TranslateEntriesActionTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();
        $action = app(TranslateEntriesAction::class);

        $collection = Collection::make('pages');
        $collection->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        $entry = $entry->collection($collection);
        $entry = $entry->data(['title' => 'foo']);
        $entry->save();

        $entries = collect([$entry]);
        $action->run($entries, [
            'to_sites' => [
                'pt',
            ],
        ]);

        Bus::assertDispatched(TranslateEntriesJob::class, 1);
    }

    #[Test]
    public function it_can_dispatch_jobs_for_all(): void
    {
        Bus::fake();
        $action = app(TranslateEntriesAction::class);

        $collection = Collection::make('pages');
        $collection->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        $entry = $entry->collection($collection);
        $entry = $entry->data(['title' => 'foo']);
        $entry->save();

        $entries = collect([$entry]);
        $action->run($entries, [
            'to_sites' => [
                'all',
            ],
        ]);

        Bus::assertDispatched(TranslateEntriesJob::class, 1);
    }

    #[Test]
    public function it_will_not_dispatch_without_source(): void
    {
        Bus::fake();
        $action = app(TranslateEntriesAction::class);

        $entries = collect([null]);
        $action->run($entries, [
            'to_sites' => [
                'all',
            ],
        ]);

        Bus::assertDispatched(TranslateEntriesJob::class, 0);
    }

    #[Test]
    public function it_has_field_items(): void
    {
        $action = app(TranslateEntriesAction::class);
        $items = $action->fieldItems();

        $this->assertSame([
            'to_sites' => [
                'type' => 'checkboxes',
                'display' => 'Translate to',
                'instructions' => 'Select the target sites to translate to',
                'options' => [
                    'en' => 'English - EN',
                    'nl' => 'Dutch - NL',
                    'pt' => 'Dutch - PT',
                    'all' => 'All sites',
                ],
                'validate' => 'required|array|min:1',
            ],
        ], $items);
    }

    #[Test]
    public function it_has_rules(): void
    {
        $action = app(TranslateEntriesAction::class);
        $items = $action->rules();

        $this->assertSame([
            'from_site' => 'required',
            'to_sites' => 'required|array|min:1',
            'to_sites.*' => 'different:from_site',
        ], $items);
    }

    #[Test]
    public function it_has_messages(): void
    {
        $action = app(TranslateEntriesAction::class);
        $items = $action->messages();

        $this->assertSame([
            'to_sites.*.different' => 'Target sites must be different from the source site',
            'to_sites.required' => 'Please select at least one target site',
            'to_sites.min' => 'Please select at least one target site',
        ], $items);
    }

    #[Test]
    public function it_has_a_title(): void
    {
        $action = app(TranslateEntriesAction::class);
        $title = $action->title();

        $this->assertSame(__('Translate Content'), $title);
    }
}
