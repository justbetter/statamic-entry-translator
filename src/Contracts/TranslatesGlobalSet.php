<?php

namespace JustBetter\EntryTranslator\Contracts;

use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

interface TranslatesGlobalSet
{
    public function translate(GlobalSet $source, Site $sourceSite, Site $targetSite): void;
}
