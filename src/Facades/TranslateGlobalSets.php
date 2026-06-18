<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSets;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

/**
 * @method static void translateGlobalSets(GlobalSet $source, Site $sourceSite, Collection<int, Site> $targetSites)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesGlobalSets
 */
class TranslateGlobalSets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesGlobalSets::class;
    }
}
