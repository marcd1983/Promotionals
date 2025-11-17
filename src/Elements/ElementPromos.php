<?php

namespace Antlion\Promotionals\Elements;

use DNADesign\Elemental\Models\BaseElement;
use Antlion\Promotionals\Model\Promo;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\GridFieldArchiveAction;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataList;

use Antlion\Promotionals\Controllers\ElementPromosController;

/**
 * Class ElementPromos
 *
 * @property string $Content
 * @method ManyManyList|Promo[] Promos()
 */
class ElementPromos extends BaseElement
{

    private static $icon = 'font-icon-block-banner';
    private static $singular_name = 'Promos Element';
    private static $plural_name = 'Promos Elements';
    private static $table_name = 'ElementPromos';


    private static $controller_class = ElementPromosController::class;

    /**
     * Set to false to prevent an in-line edit form from showing in an elemental area. Instead the element will be
     * clickable and a GridFieldDetailForm will be used.
     *
     * @config
     * @var bool
     */
    private static $inline_editable = false;

    private static $styles = array();

    private static $db = [
        'Content' => 'HTMLText',

        'Appearance'     => 'Enum("Grid,Carousel","Grid")',

        'Loop'            => 'Boolean',
        'Speed'           => 'Int',
        'SpaceBetween'    => 'Int',
        'SlidesPerView'   => 'Int',   // desktop
        'SlidesPerViewMd' => 'Int',   // tablet
        'SlidesPerViewSm' => 'Int',   // mobile
        'CenteredSlides'  => 'Boolean',
        'FreeMode'        => 'Boolean',
        'Pagination'      => 'Boolean',
        'Navigation'      => 'Boolean',
        'Scrollbar'       => 'Boolean',
        'MouseWheel'      => 'Boolean',
        'Autoplay'        => 'Boolean',
        'AutoplayDelay'   => 'Int',
        'Lazy'            => 'Boolean',
    ];

    public function populateDefaults()
    {
        $this->owner->Speed         = 600;
        $this->owner->SpaceBetween  = 20;
        $this->SlidesPerView        = 5;
        $this->SlidesPerViewMd      = 3;
        $this->SlidesPerViewSm      = 1;
        $this->owner->Pagination    = true;
        $this->owner->Navigation    = true;
        $this->owner->Loop          = true;
        $this->owner->Autoplay      = true;
        $this->owner->AutoplayDelay = 5000;
        parent::populateDefaults();
    }

    private static $many_many = array(
        'Promos' => Promo::class,
    );

    private static $many_many_extraFields = array(
        'Promos' => array(
            'SortOrder' => 'Int',
        ),
    );

    /**
     * @param bool $includerelations
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Content']    = _t(__CLASS__.'.ContentLabel', 'Intro');
        $labels['Promos']     = _t(__CLASS__.'.PromosLabel', 'Promos');
        $labels['Appearance'] = _t(__CLASS__.'.Appearance', 'Appearance');

        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            // Intro copy
            $fields->dataFieldByName('Content')?->setRows(5);

            // Appearance dropdown (Grid / Carousel)
            $fields->insertBefore(
                'Content',
                DropdownField::create('Appearance', $this->fieldLabel('Appearance'), [
                    'Grid'      => 'Grid',
                    'Carousel'  => 'Carousel',
                ])->setEmptyString('-- choose --')
            );

            // Promos grid config
            if ($this->ID) {
                $promoField = $fields->dataFieldByName('Promos');
                if ($promoField) {
                    $fields->removeByName('Promos');
                    $cfg = $promoField->getConfig();
                    $cfg->removeComponentsByType([
                        GridFieldAddExistingAutocompleter::class,
                        GridFieldDeleteAction::class,
                        GridFieldArchiveAction::class,
                    ])->addComponents(
                        new GridFieldOrderableRows('SortOrder'),
                        new GridFieldAddExistingSearchButton()
                    );
                    $fields->addFieldToTab('Root.Main', $promoField);
                }
            }

            $fields->removeByName ([
                'Loop',
                'SortOrder',  
                'ParentID', 
                'Theme', 
                'Align', 
                'OverlayOpacity', 
                'StartDate', 
                'EndDate',
                'Speed',
                'SpaceBetween',
                'SlidesPerView',
                'SlidesPerViewMd',
                'SlidesPerViewSm',
                'CenteredSlides',
                'FreeMode',
                'Pagination',
                'Navigation',
                'Scrollbar',
                'MouseWheel',
                'Autoplay',
                'AutoplayDelay',
                'Lazy',
                'Slides',
                
            ]);

            // Carousel settings group (always visible; optionally you can add display logic)
            $fields->addFieldToTab(
                'Root.Main',
                ToggleCompositeField::create(
                    'CarouselSettings',
                    'Carousel settings',
                    [
                        NumericField::create('SlidesPerView',   'Slides per view (desktop)'),
                        NumericField::create('SlidesPerViewMd', 'Slides per view (tablet ≥ 640px)'),
                        NumericField::create('SlidesPerViewSm', 'Slides per view (mobile < 640px)'),
                        NumericField::create('SpaceBetween',    'Space between slides (px)'),
                        CheckboxField::create('Loop',           'Loop'),
                        CheckboxField::create('Pagination',     'Pagination'),
                        CheckboxField::create('Navigation',     'Navigation (prev/next arrows)'),
                        CheckboxField::create('Scrollbar',      'Scrollbar'),
                        CheckboxField::create('MouseWheel', 'Mousewheel control'),
                        CheckboxField::create('Lazy',           'Lazy images'),
                        CheckboxField::create('CenteredSlides', 'Centered slides'),
                        CheckboxField::create('FreeMode',       'Free mode (drag slides)'),
                        CheckboxField::create('Autoplay',       'Autoplay'),
                        NumericField::create('AutoplayDelay',   'Autoplay delay (ms)')
                            ->setDescription('Used only when Autoplay is enabled.'),
                        NumericField::create('Speed',           'Transition speed (ms)'),
                    ]
                )->setStartClosed(true)
            );
        });

        return parent::getCMSFields();
    }

    /**
     * @return mixed
     */
    // public function getPromoList()
    // {
    //     return Promo::getActive()
    //     ->sort(['IsFeatured' => 'DESC', 'Created' => 'DESC']);
    // }

    public function getPromoList(): DataList
    {
        return $this->Promos()
            ->where(Promo::activeFilterSQL())
            ->sort([
                'SortOrder'  => 'ASC',   // extra field on the join table
                'IsFeatured' => 'DESC',  // columns on Promo
                'Created'    => 'DESC',
            ]);
    }

    /**
     * @return DBHTMLText
     */
    public function getSummary()
    {
        $count = $this->Promos()->count();
        $label = _t(
            static::class . '.PLURALS',
            'A promo|{count} promos',
            [ 'count' => $count ]
        );
        
        return DBField::create_field('HTMLText', $label)->Summary(20);
    }

    /**
     * @return array
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();
        return $blockSchema;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__.'.BlockType', 'Promos');
    }

    /**
     * Build a Swiper options array from the DB config.
     */
    public function getCarouselOptions(): array
    {
        $o = [
            'effect'          => 'slide',
            'loop'            => (bool) $this->owner->Loop,
            'speed'           => (int)  ($this->owner->Speed ?: 600),
            'spaceBetween' => (int)($this->SpaceBetween ?: 0),
            'centeredSlides' => (bool)$this->owner->CenteredSlides,
            'breakpoints' => [
                0    => ['slidesPerView' => (int)($this->SlidesPerViewSm ?: 1)],
                640  => ['slidesPerView' => (int)($this->SlidesPerViewMd ?: 2)],
                1024 => ['slidesPerView' => (int)($this->SlidesPerView   ?: 3)],
            ],
        ];

        if ($this->owner->SlidesPerView) {
            $o['slidesPerView'] = (int) $this->owner->SlidesPerView;
        }
        
        if ($this->owner->FreeMode) {
            $o['freeMode'] = (bool) $this->owner->FreeMode;
        }

        if ($this->owner->MouseWheel) {
            $o['mousewheel'] = (bool) $this->owner->MouseWheel;
        }

        if ($this->owner->Pagination) {
            $o['pagination'] = [
                'el'        => '.swiper-pagination',
                'clickable' => true,
            ];
        }
        if ($this->owner->Navigation) {
            $o['navigation'] = [
                'nextEl' => '.swiper-button-next',
                'prevEl' => '.swiper-button-prev',
            ];
        }
        if ($this->owner->Scrollbar) {
            $o['scrollbar'] = [
                'el'   => '.swiper-scrollbar',
                'hide' => false,
            ];
        }

        if ($this->owner->Autoplay) {
            $o['autoplay'] = [
                'delay'               => (int)($this->owner->AutoplayDelay ?: 5000),
                'disableOnInteraction'=> false,
                'pauseOnMouseEnter'   => true,
            ];
        }
        if ($this->owner->Lazy) {
            $o['lazy'] = [
                'loadPrevNext' => true,
            ];
        }
        return $o;
    }

    /**
     * JSON for template injection.
     */
    public function getCarouselOptionsJSON(): string
    {
        return json_encode($this->getCarouselOptions(), JSON_UNESCAPED_SLASHES);
    }
}
