<?php

namespace JustBetter\EntryTranslator\Actions;

use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Exceptions\TranslatorNotFoundException;
use JustBetter\EntryTranslator\Translators\BaseTranslator;

class ResolveTranslator implements ResolvesTranslator
{
    public function resolve(): BaseTranslator
    {
        $service = config()->string('justbetter.statamic-entry-translator.service', 'deepl');

        /** @var array<string, array<string, class-string<BaseTranslator>>> $services */
        $services = config()->array('justbetter.statamic-entry-translator.services', []);

        if (! array_key_exists($service, $services)) {
            throw new TranslatorNotFoundException('Invalid Translator service: '.$service);
        }

        $class = $services[$service]['translator'];

        return app($class);
    }

    public static function bind(): void
    {
        app()->singleton(ResolvesTranslator::class, static::class);
    }
}
