<?php

namespace JustBetter\EntryTranslator\Contracts;

use Illuminate\Support\Collection;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

interface TranslatesGlobalSets
{
    /**
     * @param  Collection<int, Site>  $targetSites
     */
    public function translateGlobalSets(GlobalSet $source, Site $sourceSite, Collection $targetSites): void;
}
