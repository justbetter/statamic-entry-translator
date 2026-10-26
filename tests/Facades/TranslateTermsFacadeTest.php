<?php

namespace JustBetter\EntryTranslator\Tests\Facades;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Facades\TranslateTerms;
use JustBetter\EntryTranslator\Jobs\TranslateTermJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateTermsFacadeTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        $term = $this->makeTerm();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = collect([Site::get('nl'), Site::get('pt')]);

        TranslateTerms::translateTerms($term, $sites);

        Bus::assertDispatched(TranslateTermJob::class, 2);
    }
}
