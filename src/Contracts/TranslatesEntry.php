<?php

namespace JustBetter\EntryTranslator\Contracts;

use Statamic\Entries\Entry;
use Statamic\Sites\Site;

interface TranslatesEntry
{
    public function translate(Entry $source, Site $site): void;
}
