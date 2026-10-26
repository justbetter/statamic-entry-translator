<?php

namespace JustBetter\EntryTranslator\Actions;

use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\Fields\CollectsLocalisableFields;
use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTerm implements TranslatesTerm
{
    public function __construct(
        protected ResolvesTranslator $resolvesTranslator,
        protected CollectsLocalisableFields $collectFields
    ) {}

    public function translate(LocalizedTerm $source, Site $site): void
    {
        /** @var LocalizedTerm $term */
        $term = $source->in($site->handle());

        $localisableFields = $term->blueprint()->fields()->localizable()->items();
        /** @var Collection<int, non-falsy-string> $localisableFields */
        $localisableFields = $this->collectFields->collect($localisableFields);

        $data = $this->resolvesTranslator->resolve()->translate($source, $localisableFields, $site);

        /** @var Collection<string, mixed> $original */
        $original = $source->data();

        $term->data($original->replaceRecursive($data));
        $term->saveQuietly();
    }

    public static function bind(): void
    {
        app()->bind(TranslatesTerm::class, static::class);
    }
}
