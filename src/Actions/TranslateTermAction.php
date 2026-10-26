<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Jobs\TranslateTermsJob;
use Statamic\Actions\Action;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermAction extends Action
{
    // @phpstan-ignore-next-line missingType.parameter
    public function run($items, $values): array
    {
        /** @var Collection<int, LocalizedTerm> $items */
        $items = $items;
        /** @var array<string, array<int, string>> $values */
        $values = $values;

        $sourceSite = $this->sourceSite();
        $all = collect($values['to_sites'])->some('all');

        foreach ($items as $item) {
            /** @var LocalizedTerm $source */
            $source = $item->in($sourceSite->handle());
            $targetSites = $this->targetSites($source, $sourceSite, $all, $values['to_sites']);

            if ($targetSites->isNotEmpty()) {
                TranslateTermsJob::dispatch($source, $targetSites);
            }
        }

        $message = __('Terms are added in the queue. It can take a little bit of time to be processed.');

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
        $sites = $source instanceof LocalizedTerm
            ? $this->taxonomySites($source)
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
        return $item instanceof LocalizedTerm;
    }

    public function authorize(mixed $user, mixed $item): bool
    {
        // @phpstan-ignore-next-line
        return $user->can('edit', $item);
    }

    protected function sourceSite(): StatamicSite
    {
        // The term action context does not contain a site and selected items are
        // resolved in the default locale, so the CP selected site is the source.
        $selected = Site::selected();

        if (! $selected instanceof StatamicSite) {
            /** @var StatamicSite $selected */
            $selected = Site::default();
        }

        return $selected;
    }

    /**
     * @param  array<int, string>  $selectedSites
     * @return Collection<int, StatamicSite>
     */
    protected function targetSites(LocalizedTerm $source, StatamicSite $sourceSite, bool $all, array $selectedSites): Collection
    {
        $availableSites = $this->taxonomySites($source)
            ->reject(fn (StatamicSite $site): bool => $site->handle() === $sourceSite->handle());

        return $all
            ? $availableSites->values()
            : $availableSites
                ->filter(fn (StatamicSite $site): bool => collect($selectedSites)->contains($site->handle()))
                ->values();
    }

    /**
     * @return Collection<int, StatamicSite>
     */
    protected function taxonomySites(LocalizedTerm $term): Collection
    {
        /** @var Collection<int, string> $handles */
        $handles = $term->taxonomy()->sites();

        return $handles
            ->map(fn (string $handle): mixed => Site::get($handle))
            ->filter(fn (mixed $site): bool => $site instanceof StatamicSite)
            ->values();
    }
}
