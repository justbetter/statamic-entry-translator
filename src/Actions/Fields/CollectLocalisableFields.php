<?php

namespace JustBetter\EntryTranslator\Actions\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\Fields\CollectsLocalisableFields;
use Statamic\Facades\Fieldset;

class CollectLocalisableFields implements CollectsLocalisableFields
{
    public function collect(Collection $fields): Collection
    {
        $excludeHandles = config()->array('justbetter.statamic-entry-translator.excluded_handles', []);
        $excludeTypes = config()->array('justbetter.statamic-entry-translator.excluded_types', []);

        return $fields
            // @phpstan-ignore-next-line
            ->flatMap(function (array|Collection $field) use ($excludeHandles, $excludeTypes) {
                if (! is_array($field)) {
                    $field = $field->toArray();
                }

                if (isset($field['import'])) {
                    $fieldset = Fieldset::find($field['import']);

                    return $fieldset
                        ? $this->collect($fieldset->fields()->localizable()->items())
                        : collect();
                }

                $handle = $field['handle'] ?? null;

                $type = Arr::get($field, 'field.type') ?? Arr::get($field, 'type');

                if ($type === 'replicator') {
                    /** @var array<int, mixed> $sets */
                    $sets = Arr::get($field, 'field.sets', []);
                    $sets = collect($sets);

                    return $this->collect($sets);
                }

                if (isset($field['sets'])) {
                    /** @var array<int, mixed> $sets */
                    $sets = $field['sets'];
                    $nested = collect($sets)
                        // @phpstan-ignore-next-line
                        ->flatMap(fn ($set) => $this->collect(collect($set['fields'] ?? [])));

                    return $nested;
                }

                if (isset($field['fields']) && is_array($field['fields'])) {
                    return $this->collect(collect($field['fields']));
                }

                if (! $handle) {
                    return collect();
                }

                if (in_array($handle, $excludeHandles)) {
                    return collect();
                }

                if ($type && in_array($type, $excludeTypes)) {
                    return collect();
                }

                return collect([$handle]);
            })
            ->filter()
            ->unique()
            ->values()
            ->push('text');
    }

    public static function bind(): void
    {
        app()->bind(CollectsLocalisableFields::class, static::class);
    }
}
