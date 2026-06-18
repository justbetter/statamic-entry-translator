<?php

namespace JustBetter\EntryTranslator\Tests\Jobs;

use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSet;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site as StatamicSite;

class TranslateGlobalSetJobTest extends TestCase
{
    #[Test]
    public function it_can_translate_global_set(): void
    {
        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')->sites(['en', 'nl']);
        /** @var StatamicSite $sourceSite */
        $sourceSite = Site::get('en');
        /** @var StatamicSite $targetSite */
        $targetSite = Site::get('nl');

        $this->mock(TranslatesGlobalSet::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translate')
                ->once();
        });

        TranslateGlobalSetJob::dispatch($globalSet, $sourceSite, $targetSite);
    }
}
