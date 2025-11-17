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
<<<<<<< HEAD
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
=======

    private static $menu_icon_class = 'font-icon-megaphone';
    // private static $menu_priority = 0.6; 
    private static $managed_models = [
        Promo::class,
        PromoCategory::class,
    ];
    private static $url_segment = 'promos';
>>>>>>> 10cedb0 (re init)
    private static $menu_title = 'Promos';
}
