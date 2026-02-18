<?php

namespace JustBetter\EntryTranslator\Tests\Translators;

use DeepL\DeepLClient;
use Illuminate\Support\Collection as SupportCollection;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class DeeplTranslatorTest extends TestCase
{
    #[Test]
    public function it_translates_data(): void
    {
        $collection = Collection::make('pages');
        $collection->save();

        /** @var Entry $entry */
        $entry = EntryFacade::make();
        $entry = $entry->collection($collection);

        $entry = $entry->data(['title' => 'foo']);
        $entry->saveQuietly();

        /** @var SupportCollection<int, StatamicSite> $sites */
        $sites = Site::all();
        /** @var Entry $entry */
        $entry = EntryFacade::find($entry->id());

        $this->mock(DeepLClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('translateText')
                ->once()
                ->with(
                    ['foo'],
                    'en-US',
                    'en-US'
                )->andReturn([(object) ['text' => 'bar']]);
        });

        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        $resolvesTranslator = app(ResolvesTranslator::class);
        /** @var array<int, non-falsy-string> */
        $fields = ['title'];

        /** @var StatamicSite $site */
        $site = $sites->first();
        $resolvesTranslator->resolve()->translate($entry, collect($fields), $site);
    }
}
