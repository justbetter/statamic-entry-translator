<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSets;
use JustBetter\EntryTranslator\Jobs\TranslateGlobalSetJob;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

class TranslateGlobalSets implements TranslatesGlobalSets
{
    /**
     * @param  Collection<int, Site>  $targetSites
     */
    public function translateGlobalSets(GlobalSet $source, Site $sourceSite, Collection $targetSites): void
    {
        foreach ($targetSites as $targetSite) {
            TranslateGlobalSetJob::dispatch($source, $sourceSite, $targetSite);
        }
    }

    public static function bind(): void
    {
        app()->bind(TranslatesGlobalSets::class, static::class);
    }
}
