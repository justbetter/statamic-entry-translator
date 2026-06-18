<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetsJob;
use Statamic\Actions\Action;
use Statamic\Facades\Site;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site as StatamicSite;

class TranslateGlobalSetAction extends Action
{
    // @phpstan-ignore-next-line missingType.parameter
    public function run($items, $values): array
    {
        /** @var Collection<int, GlobalSet> $items */
        $items = $items;
        /** @var array<string, array<int, string>> $values */
        $values = $values;

        $sourceSite = $this->sourceSite();
        $all = collect($values['to_sites'])->some('all');

        foreach ($items as $source) {
            $targetSites = $this->targetSites($source, $sourceSite, $all, $values['to_sites']);

            if ($targetSites->isNotEmpty()) {
                TranslateGlobalSetsJob::dispatch($source, $sourceSite, $targetSites);
            }
        }

        $message = __('Globals are added in the queue. It can take a little bit of time to be processed.');

        return ['message' => $message];
    }

    /**
     * @return array<string, array<string, array<string>|string>>
     */
    public function fieldItems(): array
    {
        $sourceSite = $this->sourceSite();
        $source = $this->items->first();

        /** @var Collection<int, StatamicSite> $sites */
        $sites = $source instanceof GlobalSet
            ? $source->sites()->map(fn (string $handle): ?StatamicSite => $this->site($handle))->filter()->values()
            : Site::all();

        $siteOptions = $sites
            ->reject(fn (StatamicSite $site): bool => $site->handle() === $sourceSite->handle())
            ->mapWithKeys(fn (StatamicSite $site) => [
                $site->handle() => $site->name().' - '.strtoupper($site->lang()),
            ])
            ->put('all', 'All sites')
            ->all();

        return [
            'to_sites' => [
                'type' => 'checkboxes',
                'display' => 'Translate to',
                'instructions' => 'Select the target sites to translate to',
                'options' => $siteOptions,
                'validate' => 'required|array|min:1',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'from_site' => 'required',
            'to_sites' => 'required|array|min:1',
            'to_sites.*' => 'different:from_site',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to_sites.*.different' => 'Target sites must be different from the source site',
            'to_sites.required' => 'Please select at least one target site',
            'to_sites.min' => 'Please select at least one target site',
        ];
    }

    public static function title(): string
    {
        return __('Translate Content');
    }

    public function visibleTo(mixed $item): bool
    {
        return $item instanceof GlobalSet;
    }

    public function authorize(mixed $user, mixed $item): bool
    {
        // @phpstan-ignore-next-line
        return $user->can('edit', $item);
    }

    protected function sourceSite(): StatamicSite
    {
        $contextSite = Arr::get($this->context, 'site');

        $selected = Site::selected();
        if (! $selected instanceof StatamicSite) {
            /** @var StatamicSite $selected */
            $selected = Site::default();
        }

        $handle = is_string($contextSite) ? $contextSite : $selected->handle();

        $site = $this->site($handle);

        if (! $site) {
            return $selected;
        }

        return $site;
    }

    /**
     * @param  array<int, string>  $selectedSites
     * @return Collection<int, StatamicSite>
     */
    protected function targetSites(GlobalSet $source, StatamicSite $sourceSite, bool $all, array $selectedSites): Collection
    {
        $availableSites = $source->sites()
            ->map(fn (string $handle): ?StatamicSite => $this->site($handle))
            ->filter()
            ->values();

        return $all
            ? $availableSites->reject(fn (StatamicSite $site): bool => $site->handle() === $sourceSite->handle())->values()
            : collect($selectedSites)
                ->map(fn (string $handle): ?StatamicSite => $this->site($handle))
                ->filter(fn (?StatamicSite $site): bool => $site instanceof StatamicSite
                    && $site->handle() !== $sourceSite->handle()
                    && $source->sites()->contains($site->handle()))
                ->values();
    }

    protected function site(string $handle): ?StatamicSite
    {
        $site = Site::get($handle);

        return $site instanceof StatamicSite ? $site : null;
    }
}
