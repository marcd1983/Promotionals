<?php

namespace Antlion\Promotionals\Admin;

use Antlion\Promotionals\Model\Promo;
use Antlion\Promotionals\Model\PromoCategory;
use SilverStripe\Admin\ModelAdmin;

/**
 * Class PromosAdmin
 *
 */
class PromosAdmin extends ModelAdmin
{
    /**
     * @var array
     */
    private static $managed_models = [
         Promo::class,
        PromoCategory::class,
    ];

    /**
     * @var string
     */
    private static $url_segment = 'promos';

    /**
     * @var string
     */
    private static $menu_title = 'Promos';
}
