<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use JustBetter\EntryTranslator\Jobs\TranslateTermJob;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTerms implements TranslatesTerms
{
    public function translateTerms(LocalizedTerm $source, Collection $sites): void
    {
        foreach ($sites as $site) {
            TranslateTermJob::dispatch($source, $site);
        }
    }

    public static function bind(): void
    {
        app()->bind(TranslatesTerms::class, static::class);
    }
}
