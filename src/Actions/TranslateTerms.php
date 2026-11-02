<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTerms implements TranslatesTerms
{
    public function __construct(
        protected TranslatesTerm $translatesTerm
    ) {}

    public function translateTerms(LocalizedTerm $source, Collection $sites): void
    {
        foreach ($sites as $site) {
            $this->translatesTerm->translate($source, $site);
        }
    }

    public static function bind(): void
    {
        app()->bind(TranslatesTerms::class, static::class);
    }
}
