<?php

namespace JustBetter\EntryTranslator\Translators;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Globals\Variables;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

abstract class BaseTranslator
{
    /**
     * @param  Collection<int, non-falsy-string>  $localisableFields
     * @return array<string, mixed>
     */
    abstract public function translate(Entry|Variables|LocalizedTerm $source, Collection $localisableFields, Site $site): array;
}
