<?php

namespace JustBetter\EntryTranslator\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use JustBetter\EntryTranslator\Contracts\TranslatesTerms;
use Statamic\Sites\Site;
use Statamic\Taxonomies\LocalizedTerm;

class TranslateTermsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Site>  $sites
     */
    public function __construct(
        protected LocalizedTerm $source,
        protected Collection $sites
    ) {
        $this->onQueue(config()->string('justbetter.statamic-entry-translator.queue'));
    }

    public function handle(TranslatesTerms $translatesTerms): void
    {
        $translatesTerms->translateTerms($this->source, $this->sites);
    }

    public function uniqueId(): string
    {
        return $this->source->id().'-'.$this->source->locale().'-'.$this->siteHandles()->implode('-');
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->siteHandles()
            ->prepend($this->source->locale())
            ->prepend($this->source->id())
            ->all();
    }

    /**
     * @return Collection<int, string>
     */
    protected function siteHandles(): Collection
    {
        return $this->sites->map(fn (Site $site): string => $site->handle())->values();
    }
}
