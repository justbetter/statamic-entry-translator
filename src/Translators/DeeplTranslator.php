<?php

namespace JustBetter\EntryTranslator\Translators;

use DeepL\DeepLClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

class DeeplTranslator extends BaseTranslator
{
    public function translate(Entry $source, Collection $localisableFields, Site $site): array
    {
        $deeplClient = app(DeepLClient::class);
        $data = $source->values();
        $dottedSource = Arr::dot($data);

        $dotted = collect($dottedSource)->filter(function ($value, $dottedKey) use ($localisableFields) {
            $handle = str($dottedKey)->afterLast('.')->value();

            return $localisableFields->contains($handle) && ! is_bool($value) && ! is_int($value) && ! is_float($value);
        });

        $keys = $dotted->keys()->values();
        $texts = $dotted->values()->all();

        $results = $deeplClient->translateText($texts, $source->locale(), $this->mapLanguageForDeepL((string) $site->shortLocale()));

        $translatedTexts = collect($results)
            ->map(fn ($result) => $result->text)
            ->values();

        $translatedDotted = $keys->combine($translatedTexts);

        return Arr::undot($translatedDotted);
    }

    protected function mapLanguageForDeepL(string $language): string
    {
        return match ($language) {
            'en' => 'en-US',
            'pt' => 'pt-PT',
            default => $language
        };
    }

    public static function bind(): void
    {
        app()->bind(DeepLClient::class, function () {
            $authKey = config()->string('justbetter.statamic-entry-translator.services.deepl.auth_key');

            return new DeepLClient($authKey);
        });
    }
}
