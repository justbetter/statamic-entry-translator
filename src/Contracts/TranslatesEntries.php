<?php

namespace JustBetter\EntryTranslator\Contracts;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

interface TranslatesEntries
{
    /**
     * @param  Collection<int, Site>  $sites
     */
    public function translateEntries(Entry $source, Collection $sites): void;
}
