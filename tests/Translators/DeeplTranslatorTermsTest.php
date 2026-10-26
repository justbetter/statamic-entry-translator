<?php

namespace JustBetter\EntryTranslator\Tests\Translators;

use DeepL\DeepLClient;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class DeeplTranslatorTermsTest extends TestCase
{
    #[Test]
    public function it_translates_term_data(): void
    {
        $term = $this->makeTerm(['en', 'nl'], ['title' => 'Hello']);

        $this->mock(DeepLClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateText')
                ->once()
                ->with(['Hello'], 'en-US', 'nl')
                ->andReturn([(object) ['text' => 'Hallo']]);
        });

        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        /** @var StatamicSite $site */
        $site = Site::get('nl');
        /** @var array<int, non-falsy-string> $fields */
        $fields = ['title'];

        $data = app(ResolvesTranslator::class)->resolve()->translate($term, collect($fields), $site);

        $this->assertSame(['title' => 'Hallo'], $data);
    }
}
