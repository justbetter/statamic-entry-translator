<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Actions\TranslateTerms;
use JustBetter\EntryTranslator\Jobs\TranslateTermJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateTermsTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        $term = $this->makeTerm();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = collect([Site::get('nl'), Site::get('pt')]);

        app(TranslateTerms::class)->translateTerms($term, $sites);

        Bus::assertDispatched(TranslateTermJob::class, 2);
    }
}
