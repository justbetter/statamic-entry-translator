<?php

namespace JustBetter\EntryTranslator\Contracts;

use Illuminate\Support\Collection;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

interface TranslatesTerms
{
    /**
     * @param  Collection<int, Site>  $sites
     */
    public function translateTerms(LocalizedTerm $source, Collection $sites): void;
}
