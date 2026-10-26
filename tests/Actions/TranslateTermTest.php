<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use DeepL\DeepLClient;
use JustBetter\EntryTranslator\Actions\TranslateTerm;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Statamic\Facades\Term;
use Statamic\Sites\Site as StatamicSite;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermTest extends TestCase
{
    #[Test]
    public function it_can_translate_a_term(): void
    {
        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        $this->mock(DeepLClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateText')
                ->once()
                ->with(['Hello'], 'en-US', 'nl')
                ->andReturn([(object) ['text' => 'Hallo']]);
        });

        Blueprint::make()
            ->setHandle('tags')
            ->setNamespace('taxonomies.tags')
            ->setContents([
                'title' => 'Tags',
                'tabs' => [
                    'main' => [
                        'display' => 'Main',
                        'fields' => [
                            [
                                'handle' => 'title',
                                'field' => [
                                    'type' => 'text',
                                    'localizable' => true,
                                    'display' => 'Title',
                                ],
                            ],
                            [
                                'handle' => 'enabled',
                                'field' => [
                                    'type' => 'toggle',
                                    'localizable' => true,
                                    'display' => 'Enabled',
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->save();

        $source = $this->makeTerm(data: [
            'title' => 'Hello',
            'enabled' => true,
        ]);
        $source->saveQuietly();
        /** @var StatamicSite $site */
        $site = Site::get('nl');

        app(TranslateTerm::class)->translate($source, $site);

        $savedTerm = Term::find('tags::hello');
        $this->assertInstanceOf(LocalizedTerm::class, $savedTerm);

        /** @var LocalizedTerm $translated */
        $translated = $savedTerm->in('nl');

        $this->assertSame('Hallo', $translated->get('title'));
        $this->assertTrue($translated->get('enabled'));
    }
}
