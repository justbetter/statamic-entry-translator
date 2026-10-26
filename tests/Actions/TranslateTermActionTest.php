<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use JustBetter\EntryTranslator\Actions\TranslateTermAction;
use JustBetter\EntryTranslator\Jobs\TranslateTermsJob;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermActionTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Bus::fake();

        app(TranslateTermAction::class)->run(collect([$this->makeTerm()]), [
            'to_sites' => [
                'pt',
            ],
        ]);

        Bus::assertDispatched(TranslateTermsJob::class, function (TranslateTermsJob $job): bool {
            return $job->tags() === ['tags::hello', 'en', 'pt'];
        });
    }

    #[Test]
    public function it_can_dispatch_jobs_for_all_sites(): void
    {
        Bus::fake();

        app(TranslateTermAction::class)->run(collect([$this->makeTerm()]), [
            'to_sites' => [
                'all',
            ],
        ]);

        Bus::assertDispatched(TranslateTermsJob::class, function (TranslateTermsJob $job): bool {
            return $job->tags() === ['tags::hello', 'en', 'nl', 'pt'];
        });
    }

    #[Test]
    public function it_does_not_dispatch_jobs_without_target_sites(): void
    {
        Bus::fake();

        app(TranslateTermAction::class)->run(collect([$this->makeTerm()]), [
            'to_sites' => [
                'en',
            ],
        ]);

        Bus::assertNotDispatched(TranslateTermsJob::class);
    }

    #[Test]
    public function it_has_field_items(): void
    {
        $action = app(TranslateTermAction::class)
            ->items([$this->makeTerm(['en', 'nl'])]);

        $this->assertSame([
            'to_sites' => [
                'type' => 'checkboxes',
                'display' => 'Translate to',
                'instructions' => 'Select the target sites to translate to',
                'options' => [
                    'nl' => 'Dutch - NL',
                    'all' => 'All sites',
                ],
                'validate' => 'required|array|min:1',
            ],
        ], $action->fieldItems());
    }

    #[Test]
    public function it_has_field_items_without_a_term(): void
    {
        $action = app(TranslateTermAction::class)->items([]);

        $this->assertSame([
            'nl' => 'Dutch - NL',
            'pt' => 'Dutch - PT',
            'all' => 'All sites',
        ], $action->fieldItems()['to_sites']['options']);
    }

    #[Test]
    public function it_has_rules(): void
    {
        $this->assertSame([
            'from_site' => 'required',
            'to_sites' => 'required|array|min:1',
            'to_sites.*' => 'different:from_site',
        ], app(TranslateTermAction::class)->rules());
    }

    #[Test]
    public function it_has_messages(): void
    {
        $this->assertSame([
            'to_sites.*.different' => 'Target sites must be different from the source site',
            'to_sites.required' => 'Please select at least one target site',
            'to_sites.min' => 'Please select at least one target site',
        ], app(TranslateTermAction::class)->messages());
    }

    #[Test]
    public function it_has_a_title(): void
    {
        $this->assertSame(__('Translate Content'), app(TranslateTermAction::class)->title());
    }

    #[Test]
    public function it_is_visible_to_terms(): void
    {
        $this->assertTrue(app(TranslateTermAction::class)->visibleTo($this->makeTerm()));
        $this->assertFalse(app(TranslateTermAction::class)->visibleTo(new \stdClass));
    }

    #[Test]
    public function it_authorizes_users_that_can_edit_the_item(): void
    {
        $user = new class
        {
            public function can(string $ability, mixed $item): bool
            {
                return $ability === 'edit' && $item instanceof LocalizedTerm;
            }
        };

        $this->assertTrue(app(TranslateTermAction::class)->authorize($user, $this->makeTerm()));
    }

    #[Test]
    public function it_falls_back_to_the_default_site_when_no_site_is_selected(): void
    {
        /** @var StatamicSite $default */
        $default = Site::get('en');

        Site::shouldReceive('selected')->once()->andReturn(null);
        Site::shouldReceive('default')->once()->andReturn($default);

        app(TranslateTermAction::class)->run(collect(), [
            'to_sites' => [
                'pt',
            ],
        ]);
    }
}
