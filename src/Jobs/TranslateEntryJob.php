<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\EntryTranslator\Contracts\TranslatesEntry;
use Statamic\Entries\Entry;
use Statamic\Sites\Site;

class TranslateEntryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Entry $source,
        protected Site $site
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesEntry $translatesEntry): void
    {
        $translatesEntry->translate($this->source, $this->site);
    }
}
