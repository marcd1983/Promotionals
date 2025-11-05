<?php

namespace Antlion\Promotionals\Model;

use Antlion\Promotionals\Elements\ElementPromos;
use Antlion\Promotionals\Pages\PromoPage;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DateField;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Versioned\Versioned;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\TagField\TagField;
use SilverStripe\ORM\DataList;

/**
 * Class Promo
 *
 * @method ManyManyList|ElementPromos[] ElementPromos()
 */

class Promo extends DataObject
{
    private static $table_name = 'Promo';
    private static $singular_name = 'Promo';
    private static $plural_name = 'Promos';

    private static $db = [
        'Title'       => 'Varchar(255)',
        'Summary'     => 'Text',
        'Content'     => 'HTMLText',
        'URLSegment'  => 'Varchar(255)',
        'StartDate'   => 'Date',
        'EndDate'     => 'Date',
        'IsFeatured'  => 'Boolean'
    ];

    private static $has_one = [
        'Image' => Image::class,
    ];

    private static $has_many = [
        'Links' => Link::class . '.Owner',
    ];

    private static $many_many = [
        'Categories' => PromoCategory::class,
    ];

    private static $owns = [
        'Image',
    ];

     private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Title'              => 'Title',
        'ActiveNice'         => 'Active',
        'StartDate'          => 'Starts',
        'EndDate'            => 'Ends',
        'URLSegment'         => 'Slug'
    ];

     private static $indexes = [
        'URLSegment' => true
    ];

   public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['URLSegment']);

        $fields->dataFieldByName('Summary')?->setRows(3);

        if ($img = $fields->dataFieldByName('Image')) {
            $img->setFolderName('Uploads/Promos');
        } else {
            $fields->insertAfter(
                'Title',
                UploadField::create('Image', 'Promo image')->setFolderName('Uploads/Promos')
            );
        }

        $fields->insertAfter('Title', TextField::create('URLSegment', 'URL segment')
            ->setDescription('Used in the detail page URL; leave blank to auto-generate')
        );

        $fields->addFieldToTab('Root.Main',
            MultiLinkField::create('Links', 'CTA Buttons', $this->Links())
        );

        $fields->addFieldsToTab('Root.Schedule', [
            DateField::create('StartDate')->setHTML5(true),
            DateField::create('EndDate')->setHTML5(true)
        ]);

        $tag = TagField::create('Categories', 'Categories', PromoCategory::get(), $this->Categories());
        $tag->setCanCreate(true);
        $fields->addFieldToTab('Root.Categories', $tag);

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Generate/normalise slug
        if (!$this->URLSegment) {
            $filter = URLSegmentFilter::create();
            $this->URLSegment = $filter->filter($this->Title ?: 'promo');
        }
        $this->URLSegment = $this->uniqueSlug($this->URLSegment);

        foreach ($this->Links() as $link) {
            if ($link->hasExtension(Versioned::class) && !$link->isPublished()) {
                $link->publishSingle(); // publish each link to Live
            }
        }
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;
        while (static::get()->filter('URLSegment', $slug)->exclude('ID', $this->ID ?: 0)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function Active(): bool
    {
        $today = DBDatetime::now()->Date();
        $start = $this->StartDate ?: null;
        $end   = $this->EndDate ?: null;

        if (!$start && !$end) {
            return true;
        }
        if ($start && !$end) {
            return $start <= $today;
        }
        if (!$start && $end) {
            return $today <= $end;
        }
        return $start <= $today && $today <= $end;
    }

    public static function activeFilterSQL(): string
    {
        return '("StartDate" IS NULL OR "StartDate" <= CURRENT_DATE())'
            . ' AND ("EndDate" IS NULL OR "EndDate" >= CURRENT_DATE())';
    }

    public static function getActive(): DataList
    {
        return static::get()->where(static::activeFilterSQL());
    }

    public function getActiveNice(): string
    {
        return $this->Active() ? 'Yes' : 'No';
    }
    
    public function Link(): ?string
    {
        $page = PromoPage::get()->first();
        if (!$page) {
            $home = SiteTree::get()->filter('URLSegment', 'home')->first();
            return $home ? $home->Link() : '/';
        }
        return $page->Link($this->URLSegment);
    }

}
