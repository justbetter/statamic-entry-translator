<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Actions\TranslateGlobalSetAction;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetsJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;

class TranslateGlobalSetActionTest extends TestCase
{
    protected function setUpData(): GlobalSet
    {
        /** @var GlobalSet $globalSet */
        $globalSet = GlobalSetFacade::make('settings')
            ->title('Settings')
            ->sites(['en', 'nl', 'pt']);

        return $globalSet;
    }

    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        $action = app(TranslateGlobalSetAction::class)->context(['site' => 'en']);
        $globalSet = $this->setUpData();

        $action->run(collect([$globalSet]), [
            'to_sites' => [
                'pt',
            ],
        ]);

        Bus::assertDispatched(TranslateGlobalSetsJob::class, 1);
    }

    #[Test]
    public function it_can_dispatch_jobs_for_all_sites(): void
    {
        Bus::fake();

        $action = app(TranslateGlobalSetAction::class)->context(['site' => 'en']);
        $globalSet = $this->setUpData();

        $action->run(collect([$globalSet]), [
            'to_sites' => [
                'all',
            ],
        ]);

        Bus::assertDispatched(TranslateGlobalSetsJob::class, 1);
    }

    #[Test]
    public function it_has_field_items(): void
    {
        $action = app(TranslateGlobalSetAction::class)
            ->context(['site' => 'en'])
            ->items([$this->setUpData()]);

        $this->assertSame([
            'to_sites' => [
                'type' => 'checkboxes',
                'display' => 'Translate to',
                'instructions' => 'Select the target sites to translate to',
                'options' => [
                    'nl' => 'Dutch - NL',
                    'pt' => 'Dutch - PT',
                    'all' => 'All sites',
                ],
                'validate' => 'required|array|min:1',
            ],
        ], $action->fieldItems());
    }

    #[Test]
    public function it_has_field_items_without_a_global_set_context(): void
    {
        $action = app(TranslateGlobalSetAction::class)
            ->context(['site' => 'en'])
            ->items([]);

        $this->assertSame([
            'to_sites' => [
                'type' => 'checkboxes',
                'display' => 'Translate to',
                'instructions' => 'Select the target sites to translate to',
                'options' => [
                    'nl' => 'Dutch - NL',
                    'pt' => 'Dutch - PT',
                    'all' => 'All sites',
                ],
                'validate' => 'required|array|min:1',
            ],
        ], $action->fieldItems());
    }

    #[Test]
    public function it_has_rules(): void
    {
        $action = app(TranslateGlobalSetAction::class);

        $this->assertSame([
            'from_site' => 'required',
            'to_sites' => 'required|array|min:1',
            'to_sites.*' => 'different:from_site',
        ], $action->rules());
    }

    #[Test]
    public function it_has_messages(): void
    {
        $action = app(TranslateGlobalSetAction::class);

        $this->assertSame([
            'to_sites.*.different' => 'Target sites must be different from the source site',
            'to_sites.required' => 'Please select at least one target site',
            'to_sites.min' => 'Please select at least one target site',
        ], $action->messages());
    }

    #[Test]
    public function it_has_a_title(): void
    {
        $this->assertSame(__('Translate Content'), app(TranslateGlobalSetAction::class)->title());
    }

    #[Test]
    public function it_is_visible_to_global_sets(): void
    {
        $this->assertTrue(app(TranslateGlobalSetAction::class)->visibleTo($this->setUpData()));
        $this->assertFalse(app(TranslateGlobalSetAction::class)->visibleTo(new \stdClass));
    }

    #[Test]
    public function it_authorizes_users_that_can_edit_the_item(): void
    {
        $globalSet = $this->setUpData();
        $user = new class
        {
            public function can(string $ability, mixed $item): bool
            {
                return $ability === 'edit' && $item instanceof GlobalSet;
            }
        };

        $this->assertTrue(app(TranslateGlobalSetAction::class)->authorize($user, $globalSet));
    }

    #[Test]
    public function it_falls_back_to_the_selected_site_for_an_invalid_context_site(): void
    {
        Bus::fake();

        app(TranslateGlobalSetAction::class)
            ->context(['site' => 'missing'])
            ->run(collect([$this->setUpData()]), [
                'to_sites' => [
                    'pt',
                ],
            ]);

        Bus::assertDispatched(TranslateGlobalSetsJob::class, 1);
    }

    #[Test]
    public function it_falls_back_to_the_default_site_when_no_site_is_selected(): void
    {
        /** @var \Statamic\Sites\Site $default */
        $default = Site::get('en');

        Site::shouldReceive('selected')->once()->andReturn(null);
        Site::shouldReceive('default')->once()->andReturn($default);
        Site::shouldReceive('get')->once()->with('en')->andReturn($default);

        app(TranslateGlobalSetAction::class)
            ->context(['site' => 'en'])
            ->run(collect(), [
                'to_sites' => [
                    'pt',
                ],
            ]);
    }
}
