<?php

namespace JustBetter\EntryTranslator\Translators;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

abstract class BaseTranslator
{
    /**
     * @param  Collection<int, non-falsy-string>  $localisableFields
     * @return array<string, mixed>
     */
    abstract public function translate(Entry $source, Collection $localisableFields, Site $site): array;
}
