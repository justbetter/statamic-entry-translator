<?php

namespace JustBetter\EntryTranslator\Tests\Facades;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use JustBetter\EntryTranslator\Facades\TranslateTerms;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateTermsFacadeTest extends TestCase
{
    #[Test]
    public function it_can_call_the_action(): void
    {
        $term = $this->makeTerm();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = collect([Site::get('nl'), Site::get('pt')]);

        $this->mock(TranslatesTerms::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateTerms')
                ->once();
        });

        TranslateTerms::translateTerms($term, $sites);
    }
}
