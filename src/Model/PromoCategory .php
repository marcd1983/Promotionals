<?php

namespace Antlion\Promotionals\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Parsers\URLSegmentFilter;

class PromoCategory extends DataObject
{
    private static $table_name = 'PromoCategory';

    private static $db = [
        'Title'      => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
    ];

    private static $summary_fields = [
        'Title'      => 'Title',
        'URLSegment' => 'Slug',
    ];

    private static $indexes = [
        'URLSegment' => true,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->insertAfter(
            'Title',
            TextField::create('URLSegment', 'URL segment')
                ->setDescription('Leave blank to auto-generate from title')
        );
        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->URLSegment) {
            $this->URLSegment = URLSegmentFilter::create()->filter($this->Title ?: 'category');
        }
        $this->URLSegment = $this->uniqueSlug($this->URLSegment);
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base; $i = 2;
        while (static::get()->filter('URLSegment', $slug)->exclude('ID', $this->ID ?: 0)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
