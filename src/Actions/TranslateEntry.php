<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\Fields\CollectsLocalisableFields;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Contracts\TranslatesEntry;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

class TranslateEntry implements TranslatesEntry
{
    public function __construct(
        protected ResolvesTranslator $resolvesTranslator,
        protected CollectsLocalisableFields $collectFields
    ) {}

    public function translate(Entry $source, Site $site): void
    {
        $entry = $source->in($site->handle());
        $translator = $this->resolvesTranslator->resolve();

        if (! $entry) {
            $entry = $source->makeLocalization($site->handle());
        }

        $localisableFields = $entry->blueprint()->fields()->localizable()->items();
        /** @var Collection<int, non-falsy-string> $localisableFields */
        $localisableFields = $this->collectFields->collect($localisableFields);

        $data = $translator->translate($source, $localisableFields, $site);
        $original = $source->data()->all();

        $entry->data(array_replace_recursive($original, $data));

        $entry->save();
    }

    public static function bind(): void
    {
        app()->bind(TranslatesEntry::class, static::class);
    }
}
