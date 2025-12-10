<?php

namespace Antlion\Promotionals\Admin;

use Antlion\Promotionals\Model\Promo;
use Antlion\Promotionals\Model\PromoCategory;
use SilverStripe\Admin\ModelAdmin;

class PromosAdmin extends ModelAdmin
{
    private static $menu_icon_class = 'font-icon-rocket';
    // private static $menu_priority = 0.6; 
    private static $managed_models = [
        Promo::class,
        PromoCategory::class,
    ];
    private static $url_segment = 'promos';
    private static $menu_title = 'Promos';
}
