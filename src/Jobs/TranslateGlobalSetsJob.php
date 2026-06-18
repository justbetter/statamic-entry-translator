<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSets;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

class TranslateGlobalSetsJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Site>  $targetSites
     */
    public function __construct(
        protected GlobalSet $source,
        protected Site $sourceSite,
        protected Collection $targetSites
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesGlobalSets $translatesGlobalSets): void
    {
        $translatesGlobalSets->translateGlobalSets($this->source, $this->sourceSite, $this->targetSites);
    }
}
