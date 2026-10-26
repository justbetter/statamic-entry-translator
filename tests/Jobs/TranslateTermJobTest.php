<?php

namespace JustBetter\EntryTranslator\Tests\Jobs;

use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use JustBetter\EntryTranslator\Jobs\TranslateTermJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermJobTest extends TestCase
{
    #[Test]
    public function it_can_translate_a_term(): void
    {
        $this->mock(TranslatesTerm::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translate')
                ->once();
        });

        TranslateTermJob::dispatch(...$this->arguments());
    }

    #[Test]
    public function it_has_a_unique_id_and_tags(): void
    {
        $job = new TranslateTermJob(...$this->arguments());

        $this->assertSame('tags::hello-en-nl', $job->uniqueId());
        $this->assertSame(['tags::hello', 'en', 'nl'], $job->tags());
    }

    /**
     * @return array{0: LocalizedTerm, 1: StatamicSite}
     */
    protected function arguments(): array
    {
        $term = $this->makeTerm(['en', 'nl']);
        /** @var StatamicSite $site */
        $site = Site::get('nl');

        return [$term, $site];
    }
}
