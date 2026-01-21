<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesEntries;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

class TranslateEntriesJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Site>  $sites
     */
    public function __construct(
        protected Entry $source,
        protected Collection $sites
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesEntries $translatesEntries): void
    {
        $translatesEntries->translateEntries($this->source, $this->sites);
    }
}
