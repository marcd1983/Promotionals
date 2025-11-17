<?php

namespace Antlion\Promotionals\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\View\Parsers\URLSegmentFilter;
<<<<<<< HEAD
=======
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use Antlion\Promotionals\Pages\PromoPage;
>>>>>>> 10cedb0 (re init)

class PromoCategory extends DataObject
{
    private static $table_name = 'PromoCategory';
<<<<<<< HEAD
=======
    private static $singular_name = 'Promo Category';
    private static $plural_name = 'Promo Categories';
>>>>>>> 10cedb0 (re init)

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

<<<<<<< HEAD
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
=======
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
>>>>>>> 10cedb0 (re init)
        $fields->insertAfter(
            'Title',
            TextField::create('URLSegment', 'URL segment')
                ->setDescription('Leave blank to auto-generate from title')
        );
<<<<<<< HEAD
        return $fields;
    }

    public function onBeforeWrite()
=======

        $fields->addFieldToTab(
            'Root.Main',
            UploadField::create('Image', 'Category image')
                ->setFolderName('Uploads/PromoCategories')
        );

        return $fields;
    }

    public function onBeforeWrite(): void
>>>>>>> 10cedb0 (re init)
    {
        parent::onBeforeWrite();
        if (!$this->URLSegment) {
            $this->URLSegment = URLSegmentFilter::create()->filter($this->Title ?: 'category');
        }
        $this->URLSegment = $this->uniqueSlug($this->URLSegment);
    }

    protected function uniqueSlug(string $base): string
    {
<<<<<<< HEAD
        $slug = $base; $i = 2;
        while (static::get()->filter('URLSegment', $slug)->exclude('ID', $this->ID ?: 0)->exists()) {
            $slug = $base . '-' . $i++;
        }
=======
        $slug = $base;
        $i = 2;

        $list = static::get();
        if ($this->PromoPageID) {
            $list = $list->filter('PromoPageID', $this->PromoPageID);
        }

        while ($list->filter('URLSegment', $slug)->exclude('ID', $this->ID ?: 0)->exists()) {
            $slug = $base . '-' . $i++;
        }

>>>>>>> 10cedb0 (re init)
        return $slug;
    }
}
