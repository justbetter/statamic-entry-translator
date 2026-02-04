<?php

namespace JustBetter\EntryTranslator\Tests\Facades;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Facades\TranslateEntries;
use JustBetter\EntryTranslator\Jobs\TranslateEntryJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateEntriesFacadeTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        /** @var Entry $entry */
        $entry = EntryFacade::make();

        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = Site::all();
        TranslateEntries::translateEntries($entry, $sites);

        Bus::assertDispatched(TranslateEntryJob::class, 3);
    }
}
