<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\EntryTranslator\Contracts\TranslatesGlobalSet;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

class TranslateGlobalSetJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected GlobalSet $source,
        protected Site $sourceSite,
        protected Site $targetSite
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesGlobalSet $translatesGlobalSet): void
    {
        $translatesGlobalSet->translate($this->source, $this->sourceSite, $this->targetSite);
    }
}
