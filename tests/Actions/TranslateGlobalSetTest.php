<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use DeepL\DeepLClient;
use JustBetter\EntryTranslator\Actions\TranslateGlobalSet;
use JustBetter\EntryTranslator\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Blueprint;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site as StatamicSite;

class TranslateGlobalSetTest extends TestCase
{
    protected function setUpData(): GlobalSet
    {
        Blueprint::make()
            ->setHandle('settings')
            ->setNamespace('globals')
            ->setContents([
                'title' => 'Settings',
                'tabs' => [
                    'main' => [
                        'display' => 'Main',
                        'fields' => [
                            [
                                'handle' => 'title',
                                'localizable' => true,
                                'field' => [
                                    'type' => 'text',
                                    'localizable' => true,
                                    'display' => 'Title',
                                ],
                            ],
                            [
                                'handle' => 'enabled',
                                'localizable' => true,
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

        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')
            ->title('Settings')
            ->sites(['en', 'nl', 'pt']);
        $globalSet->saveQuietly();

        $variables = $globalSet->in('en');
        $this->assertNotNull($variables);
        $variables->data([
            'title' => 'Hello',
            'enabled' => true,
        ])->saveQuietly();

        return $globalSet;
    }

    #[Test]
    public function it_can_translate_a_global_set(): void
    {
        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        $this->mock(DeepLClient::class, function (MockInterface $mock): void {
            $mock->shouldReceive('translateText')
                ->once()
                ->with(['Hello'], 'en-US', 'nl')
                ->andReturn([(object) ['text' => 'Hallo']]);
        });

        $globalSet = $this->setUpData();
        $action = app(TranslateGlobalSet::class);

        /** @var StatamicSite $sourceSite */
        $sourceSite = Site::get('en');
        /** @var StatamicSite $targetSite */
        $targetSite = Site::get('nl');

        $action->translate($globalSet, $sourceSite, $targetSite);

        $savedGlobalSet = GlobalSetFacade::find('settings');
        $this->assertInstanceOf(GlobalSet::class, $savedGlobalSet);

        $translated = $savedGlobalSet->in('nl');
        $this->assertNotNull($translated);

        $this->assertSame('Hallo', $translated->get('title'));
        $this->assertTrue($translated->get('enabled'));
    }

    #[Test]
    public function it_does_not_translate_when_the_target_variables_do_not_exist(): void
    {
        config()->set('justbetter.statamic-entry-translator.service', 'deepl');
        config()->set('justbetter.statamic-entry-translator.services.deepl.auth_key', '::auth-key::');

        $this->mock(DeepLClient::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('translateText');
        });

        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')
            ->title('Settings')
            ->sites(['en']);
        $globalSet->saveQuietly();

        /** @var StatamicSite $sourceSite */
        $sourceSite = Site::get('en');
        /** @var StatamicSite $targetSite */
        $targetSite = Site::get('nl');

        app(TranslateGlobalSet::class)->translate($globalSet, $sourceSite, $targetSite);

        $this->assertNull($globalSet->in('nl'));
    }
}
