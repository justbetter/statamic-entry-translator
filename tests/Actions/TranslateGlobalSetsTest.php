<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Actions\TranslateGlobalSets;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site as StatamicSite;

class TranslateGlobalSetsTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')->sites(['en', 'nl', 'pt']);

        /** @var StatamicSite $sourceSite */
        $sourceSite = Site::get('en');
        /** @var SupportCollection<int, StatamicSite> $targetSites */
        $targetSites = collect([Site::get('nl'), Site::get('pt')]);

        app(TranslateGlobalSets::class)->translateGlobalSets($globalSet, $sourceSite, $targetSites);

        Bus::assertDispatched(TranslateGlobalSetJob::class, 2);
    }
}
