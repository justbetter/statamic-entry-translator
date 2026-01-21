<?php

namespace JustBetter\EntryTranslator\Contracts\Fields;

use Illuminate\Support\Collection;

interface CollectsLocalisableFields
{
    /**
     * @param  Collection<int, mixed>  $fields
     * @return Collection<int, non-falsy-string>
     */
    public function collect(Collection $fields): Collection;
}
