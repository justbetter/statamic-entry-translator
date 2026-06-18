<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\Fields\CollectsLocalisableFields;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSet;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

class TranslateGlobalSet implements TranslatesGlobalSet
{
    public function __construct(
        protected ResolvesTranslator $resolvesTranslator,
        protected CollectsLocalisableFields $collectFields
    ) {}

    public function translate(GlobalSet $source, Site $sourceSite, Site $targetSite): void
    {
        $sourceVariables = $source->in($sourceSite->handle());
        $targetVariables = $source->in($targetSite->handle());

        if (! $sourceVariables || ! $targetVariables) {
            return;
        }

        $localisableFields = $sourceVariables->blueprint()->fields()->localizable()->items();
        /** @var Collection<int, non-falsy-string> $localisableFields */
        $localisableFields = $this->collectFields->collect($localisableFields);

        $data = $this->resolvesTranslator->resolve()->translate($sourceVariables, $localisableFields, $targetSite);
        $original = $sourceVariables->data()->all();

        $targetVariables->data(array_replace_recursive($original, $data));
        $targetVariables->saveQuietly();
    }

    public static function bind(): void
    {
        app()->bind(TranslatesGlobalSet::class, static::class);
    }
}
