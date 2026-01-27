<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Contracts\TranslatesEntries;
use JustBetter\EntryTranslator\Jobs\TranslateEntryJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateEntriesTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();
        $action = app(TranslatesEntries::class);

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = Site::all();
        $action->translateEntries($entry, $sites);

        Bus::assertDispatched(TranslateEntryJob::class, 3);
    }
}
