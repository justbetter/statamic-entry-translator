<?php

namespace JustBetter\EntryTranslator\Tests\Actions\Fields;

use JustBetter\EntryTranslator\Contracts\Fields\CollectsLocalisableFields;
use JustBetter\EntryTranslator\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Fieldset;

class CollectLocalisableFieldsTest extends TestCase
{
    #[Test]
    public function it_will_only_collect_present_fields(): void
    {
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            ['handle' => 'bar'],
        ]));

        $this->assertEquals([
            'bar',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function it_can_handle_collection_fields(): void
    {
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            collect(['handle' => 'bar']),
        ]));

        $this->assertEquals([
            'bar',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function it_can_collect_fieldsets(): void
    {
        $action = app(CollectsLocalisableFields::class);

        Fieldset::make('seo')
            ->setContents([
                'title' => 'SEO',
                'handle' => 'seo',
                'localisable' => true,
                'fields' => [
                    [
                        'handle' => 'description',
                        'field' => [
                            'type' => 'textarea',
                            'localisable' => true,
                            'display' => 'Description',
                            'instructions' => 'Meta description',
                            'character_limit' => 160,
                        ],
                    ],
                ],
            ])
            ->save();
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            ['handle' => 'seo', 'import' => 'seo'],
        ]));

        $this->assertEquals($fields->toArray(), ['description', 'text', 'text']);
    }

    #[Test]
    public function it_can_handle_replicator_fields(): void
    {
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            [
                'handle' => 'seo',
                'field' => [
                    'type' => 'replicator',
                    'localisable' => true,
                    'sets' => [
                        'seo' => [
                            'handle' => 'content',
                            'fields' => [
                                [
                                    'handle' => 'description',
                                    'type' => 'textarea',
                                    'localisable' => false,
                                    'display' => 'Description',
                                    'instructions' => 'Meta description',
                                    'character_limit' => 160,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]));

        $this->assertEquals([
            'description',
            'text',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function is_can_handle_imported_fieldsets(): void
    {
        $action = app(CollectsLocalisableFields::class);

        Fieldset::make('seo')
            ->setContents([
                'title' => 'SEO',
                'handle' => 'seo',
                'localisable' => true,
                'fields' => [
                    [
                        'handle' => 'description',
                        'sets' => [
                            [
                                'fields' => [
                                    [
                                        'type' => 'textarea',
                                        'localisable' => true,
                                        'display' => 'Description',
                                        'instructions' => 'Meta description',
                                        'character_limit' => 160,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->save();
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            [
                'handle' => 'seo',
                'field' => [
                    'type' => 'replicator',
                    'localisable' => true,
                    'sets' => [
                        'seo' => [
                            'handle' => 'content',
                            'sets' => [
                                [
                                    'fields' => [
                                        [
                                            'handle' => 'description',
                                            'field' => [
                                                'type' => 'textarea',
                                                'localisable' => true,
                                                'display' => 'Description',
                                                'instructions' => 'Meta description',
                                                'character_limit' => 160,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]));

        $this->assertEquals([
            'description',
            'text',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function it_ignores_ignored_fields(): void
    {
        config()->set('justbetter.statamic-entry-translator.excluded_handles', ['title']);
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            ['handle' => 'title'],
            ['handle' => 'description'],
            ['handle' => 'text'],
        ]));

        $this->assertEquals([
            'description',
            'text',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function it_ignores_ignored_types(): void
    {
        config()->set('justbetter.statamic-entry-translator.excluded_types', ['select']);
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            ['handle' => 'title', 'type' => 'select'],
            ['handle' => 'description', 'type' => 'text'],
        ]));

        $this->assertEquals([
            'description',
            'text',
        ], $fields->toArray());
    }

    #[Test]
    public function it_ignores_when_no_handle_is_set(): void
    {
        config()->set('justbetter.statamic-entry-translator.excluded_types', ['select']);
        $action = app(CollectsLocalisableFields::class);
        // @phpstan-ignore-next-line
        $fields = $action->collect(collect([
            ['type' => 'text'],
            ['handle' => 'description', 'type' => 'text'],
        ]));

        $this->assertEquals([
            'description',
            'text',
        ], $fields->toArray());
    }
}
