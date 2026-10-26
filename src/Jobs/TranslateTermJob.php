<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use JustBetter\EntryTranslator\Contracts\TranslatesTerm;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        protected LocalizedTerm $source,
        protected Site $site
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesTerm $translatesTerm): void
    {
        $translatesTerm->translate($this->source, $this->site);
    }

    public function uniqueId(): string
    {
        return $this->source->id().'-'.$this->source->locale().'-'.$this->site->handle();
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            $this->source->id(),
            $this->source->locale(),
            $this->site->handle(),
        ];
    }
}
