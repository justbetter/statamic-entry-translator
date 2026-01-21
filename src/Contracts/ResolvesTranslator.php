<?php

namespace JustBetter\EntryTranslator\Contracts;

use JustBetter\EntryTranslator\Translators\BaseTranslator;

interface ResolvesTranslator
{
    public function resolve(): BaseTranslator;
}
