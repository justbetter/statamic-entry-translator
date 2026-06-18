<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSet;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

/**
 * @method static void translate(GlobalSet $source, Site $sourceSite, Site $targetSite)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesGlobalSet
 */
class TranslateGlobalSet extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesGlobalSet::class;
    }
}
