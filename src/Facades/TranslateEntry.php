<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesEntry;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

/**
 * @method static void translate(Entry $source, Site $site)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesEntry
 */
class TranslateEntry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesEntry::class;
    }
}
