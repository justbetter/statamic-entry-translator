<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesEntries;
use JustBetter\EntryTranslator\Jobs\TranslateEntryJob;
use Statamic\Entries\Entry;

class TranslateEntries implements TranslatesEntries
{
    public function translateEntries(Entry $source, Collection $sites): void
    {
        foreach ($sites as $site) {
            TranslateEntryJob::dispatch($source, $site);
        }
    }

    public static function bind(): void
    {
        app()->bind(TranslatesEntries::class, static::class);
    }
}
