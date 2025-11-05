<?php
namespace Antlion\Promotionals\Pages;

use Page;
use Antlion\Promotionals\Model\Promo;

class PromoPage extends Page
{
    private static $table_name = 'PromoPage';
    private static $description = 'Lists all promos and serves detail views';

    Private static $has_many = [
        'Promos' => Promo::class,
    ];
}
