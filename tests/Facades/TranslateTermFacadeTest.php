<?php

namespace JustBetter\EntryTranslator\Tests\Facades;

use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use JustBetter\EntryTranslator\Facades\TranslateTerm;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateTermFacadeTest extends TestCase
{
    #[Test]
    public function it_can_call_the_action(): void
    {
        $term = $this->makeTerm(['en', 'nl']);
        /** @var StatamicSite $site */
        $site = Site::get('nl');

        $this->mock(TranslatesTerm::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translate')
                ->once();
        });

        TranslateTerm::translate($term, $site);
    }
}
