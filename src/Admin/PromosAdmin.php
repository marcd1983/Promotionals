<?php

namespace Antlion\Promotionals\Admin;

use Antlion\Promotionals\Model\Promo;
use Antlion\Promotionals\Model\PromoCategory;
use SilverStripe\Admin\ModelAdmin;

class PromosAdmin extends ModelAdmin
{
<<<<<<< HEAD
    private static $menu_icon_class = 'font-icon-rocket';
=======
    private static $menu_icon_class = 'font-icon-megaphone';
>>>>>>> cb6ebb5974bce53e18e8428bbb7b2af84a3d62d7
    // private static $menu_priority = 0.6; 
    private static $managed_models = [
        Promo::class,
        PromoCategory::class,
    ];
    private static $url_segment = 'promos';
    private static $menu_title = 'Promos';
}
