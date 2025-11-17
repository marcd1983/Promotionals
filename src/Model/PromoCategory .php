<?php

namespace Antlion\Promotionals\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use Antlion\Promotionals\Pages\PromoPage;

class PromoCategory extends DataObject
{
    private static $table_name = 'PromoCategory';
    private static $singular_name = 'Promo Category';
    private static $plural_name = 'Promo Categories';

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
   
    private static $has_one = [
       
        'Image' => Image::class,
        'PromoPage' => PromoPage::class
    ];

    private static $belongs_many_many = [
        'Promos' => Promo::class,
    ];

    private static $owns = [
        'Image',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Promos']);
        $fields->insertAfter(
            'Title',
            TextField::create('URLSegment', 'URL segment')
                ->setDescription('Leave blank to auto-generate from title')
        );
        return $fields;
    }

    public function onBeforeWrite(): void
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

        $slug = $base;
        $i = 2;

        $list = static::get();
        if ($this->PromoPageID) {
            $list = $list->filter('PromoPageID', $this->PromoPageID);
        }

        while ($list->filter('URLSegment', $slug)->exclude('ID', $this->ID ?: 0)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
