<?php

namespace JustBetter\EntryTranslator\Contracts;

use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

interface TranslatesTerm
{
    public function translate(LocalizedTerm $source, Site $site): void;
}
