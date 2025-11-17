<?php
namespace Antlion\Promotionals\Pages;

use Page;
use Antlion\Promotionals\Model\Promo;
<<<<<<< HEAD
=======
use Antlion\Promotionals\Model\PromoCategory;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
>>>>>>> 10cedb0 (re init)

class PromoPage extends Page
{
    private static $table_name = 'PromoPage';
    private static $description = 'Lists all promos and serves detail views';

<<<<<<< HEAD
    Private static $has_many = [
        'Promos' => Promo::class,
    ];
=======
    private static $db = [
        'MailTo'     => 'Varchar(255)',   // fallback recipient (eg: support@site.com)
        'FormSubjectPrefix' => 'Varchar(255)', // eg: "[Promo Enquiry]"
    ];

    private static $has_many = [
        'Promos' => Promo::class,
        'PromoCategories' => PromoCategory::class
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        // --- Promo Recipient settings tab ---
        $fields->addFieldsToTab('Root.PromoRecipient', [
            TextField::create('MailTo', 'Send promo enquiries to')
                ->setDescription('Defaults to SiteConfig.SupportEmail if blank'),
            TextField::create('FormSubjectPrefix', 'Email subject prefix')
                ->setDescription('Optional, e.g. "[Promo Enquiry]"'),
        ]);

        // --- Promos tab ---
        $fields->addFieldToTab(
            'Root.Promos',
            GridField::create(
                'Promos',
                'Promos',
                $this->Promos(),
                GridFieldConfig_RecordEditor::create()
            )
        );

        // --- Promo Categories tab ---
        $fields->addFieldToTab(
            'Root.PromoCategories',
            GridField::create(
                'PromoCategories',
                'Promo Categories',
                $this->PromoCategories(),
                GridFieldConfig_RecordEditor::create()
            )
        );

        return $fields;
    }
>>>>>>> 10cedb0 (re init)
}
