<?php

namespace JustBetter\EntryTranslator\Tests\Jobs;

use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use JustBetter\EntryTranslator\Jobs\TranslateTermsJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermsJobTest extends TestCase
{
    #[Test]
    public function it_can_translate_terms(): void
    {
        $this->mock(TranslatesTerms::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateTerms')
                ->once();
        });

        TranslateTermsJob::dispatch(...$this->arguments());
    }

    #[Test]
    public function it_has_a_unique_id_and_tags(): void
    {
        $job = new TranslateTermsJob(...$this->arguments());

        $this->assertSame('tags::hello-en-nl-pt', $job->uniqueId());
        $this->assertSame(['tags::hello', 'en', 'nl', 'pt'], $job->tags());
    }

    /**
     * @return array{0: LocalizedTerm, 1: SupportCollection<int, StatamicSite>}
     */
    protected function arguments(): array
    {
        $term = $this->makeTerm();
        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = collect([Site::get('nl'), Site::get('pt')]);

        return [$term, $sites];
    }
}
