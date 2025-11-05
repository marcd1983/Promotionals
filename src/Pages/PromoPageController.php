<?php
namespace Antlion\Promotionals\Pages;


use Antlion\Promotionals\Model\Promo;
use SilverStripe\ORM\DataList;
use PageController;
use SilverStripe\Control\HTTPRequest;

class PromoPageController extends PageController
{
    private static $allowed_actions = ['show'];

    
    private static $url_handlers = [
        // put explicit actions first (e.g. category/$Slug) so they don't get eaten by the catch-all
        // 'category/$Slug' => 'category',
        '$Slug!' => 'show', // catch-all: /special-offers/<slug>
    ];


    protected function init()
            {
                parent::init();
                // You can include any CSS or JS required by your project here.
                // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/
            }

    public function Promos(): DataList
    {
         return Promo::getActive()
        ->sort(['IsFeatured' => 'DESC', 'Created' => 'DESC']);
    }

    public function show(HTTPRequest $request)
    {
        $slug = $request->param('Slug');
        $promo = Promo::getActive()->filter('URLSegment', $slug)->first();
        if (!$promo) {
            return $this->httpError(404);
        }
        return ['Promo' => $promo];
    }

}
