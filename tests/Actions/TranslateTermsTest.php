<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Actions\TranslateTerms;
use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateTermsTest extends TestCase
{
    #[Test]
    public function it_translates_each_site(): void
    {
        $this->mock(TranslatesTerm::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translate')
                ->twice();
        });

        $term = $this->makeTerm();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = collect([Site::get('nl'), Site::get('pt')]);

        app(TranslateTerms::class)->translateTerms($term, $sites);
    }
}
