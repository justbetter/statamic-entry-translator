<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Jobs\TranslateEntriesJob;
use Statamic\Actions\Action;
use Statamic\Entries\Entry;
use Statamic\Facades\Site;
use Statamic\Sites\Site as StatamicSite;

class TranslateEntriesAction extends Action
{
    // @phpstan-ignore-next-line missingType.parameter
    public function run($items, $values): array
    {
        /** @var Collection<int, Entry> $items */
        $items = $items;
        /** @var array<string, array<int, string>> $values */
        $values = $values;
        $all = collect($values['to_sites'])->some('all');
        /** @var Collection<int, StatamicSite   > $sites */
        $sites = Site::all();

        $toSites = $all
            ? $sites
                ->filter(function (StatamicSite $site) {
                    /** @var StatamicSite $selected */
                    $selected = Site::selected();

                    return $site->handle() !== $selected->handle();
                })
            : collect($values['to_sites'])
                ->map(function ($handle): StatamicSite {
                    /** @var StatamicSite $site */
                    $site = Site::get($handle);

                    return $site;
                });

        /** @var Entry $source */
        $source = $items->first();
        // @phpstan-ignore-next-line booleanNot.alwaysFalse
        if (! $source) {
            return ['message' => __('Can\'t find source entry')];
        }

        TranslateEntriesJob::dispatch($source, $toSites);

        $message = __('Entries are added in the queue. It can take a little bit of time to be processed.');

        return ['message' => $message];
    }

    /**
     * @return array<string, array<string, array<string>|string>>
     */
    public function fieldItems(): array
    {
        /** @var Collection<int, StatamicSite> */
        $sites = Site::all();
        $siteOptions = $sites
            ->mapWithKeys(fn (StatamicSite $site) => [
                $site->handle() => $site->name().' - '.strtoupper($site->lang()),
            ])
            ->filter(function (string $site): bool {
                /** @var StatamicSite $selected */
                $selected = Site::selected();

                return $site !== $selected->handle();
            })
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
        return $item instanceof Entry;
    }

    public function authorize(mixed $user, mixed $item): bool
    {
        // @phpstan-ignore-next-line
        return $user->can('edit', $item);
    }
}
