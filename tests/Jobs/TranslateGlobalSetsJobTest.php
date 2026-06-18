<?php

namespace JustBetter\EntryTranslator\Tests\Jobs;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSets;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetsJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site as StatamicSite;

class TranslateGlobalSetsJobTest extends TestCase
{
    #[Test]
    public function it_can_translate_global_sets(): void
    {
        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')->sites(['en', 'nl']);
        /** @var StatamicSite $sourceSite */
        $sourceSite = Site::get('en');
        /** @var SupportCollection<int, StatamicSite> $targetSites */
        $targetSites = collect([Site::get('nl')]);

        $this->mock(TranslatesGlobalSets::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateGlobalSets')
                ->once();
        });

        TranslateGlobalSetsJob::dispatch($globalSet, $sourceSite, $targetSites);
    }
}
