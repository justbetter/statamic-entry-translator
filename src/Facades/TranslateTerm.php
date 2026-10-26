<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

/**
 * @method static void translate(LocalizedTerm $source, Site $site)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesTerm
 */
class TranslateTerm extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesTerm::class;
    }
}
