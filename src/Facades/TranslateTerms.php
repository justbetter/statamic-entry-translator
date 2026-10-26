<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

/**
 * @method static void translateTerms(LocalizedTerm $source, Collection<int, Site> $sites)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesTerms
 */
class TranslateTerms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesTerms::class;
    }
}
