<?php

namespace JustBetter\EntryTranslator\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use JustBetter\EntryTranslator\Contracts\TranslatesEntries;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

/**
 * @method static void translateEntries(Entry $source, Collection<int, Site> $sites)
 *
 * @see JustBetter\EntryTranslator\Contracts\TranslatesEntries
 */
class TranslateEntries extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TranslatesEntries::class;
    }
}
