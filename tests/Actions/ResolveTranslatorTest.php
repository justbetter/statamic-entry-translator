<?php

namespace JustBetter\EntryTranslator\Tests\Actions;

use JustBetter\EntryTranslator\Contracts\ResolvesTranslator;
use JustBetter\EntryTranslator\Exceptions\TranslatorNotFoundException;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ResolveTranslatorTest extends TestCase
{
    #[Test]
    public function it_can_throw_exception(): void
    {
        config()->set('justbetter.statamic-entry-translator.service', 'invalid');
        $resolver = app(ResolvesTranslator::class);

        $this->expectException(TranslatorNotFoundException::class);
        $resolver->resolve();
    }
}
