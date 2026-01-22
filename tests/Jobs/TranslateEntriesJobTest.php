<?php

namespace JustBetter\EntryTranslator\Tests\Jobs;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\TranslatesEntries;
use JustBetter\EntryTranslator\Jobs\TranslateEntriesJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateEntriesJobTest extends TestCase
{
    #[Test]
    public function it_can_translate_entries(): void
    {
        $collection = Collection::make('pages');

        $collection->save();
        /** @var Entry $entry */
        $entry = EntryFacade::make();
        $entry = $entry->collection($collection);
        $entry = $entry->data(['title' => 'foo']);

        $entry->saveQuietly();

        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = Site::all();

        /** @var Entry $entry */
        $entry = EntryFacade::find($entry->id());

        $this->mock(TranslatesEntries::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateEntries')
                ->once();
        });

        TranslateEntriesJob::dispatch($entry, $sites);
    }
}
