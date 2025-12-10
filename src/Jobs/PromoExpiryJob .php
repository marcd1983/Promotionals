<?php

namespace Antlion\Promotionals\Jobs;

use Antlion\Promotionals\Model\Promo;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJob;

class PromoExpiryJob extends AbstractQueuedJob
{
    protected ?int $promoID = null;

    public function __construct(?int $promoID = null)
    {
        if ($promoID) {
            $this->promoID = $promoID;
        }
    }

    public function getSignature()
    {
        // unique key so we don't queue duplicates for same promo
        return 'PromoExpiryJob-' . ($this->promoID ?? 'none');
    }

    public function getTitle()
    {
        return 'Unpublish expired promo #' . ($this->promoID ?? 'unknown');
    }

    public function getJobType()
    {
        // Run soon after the scheduled time
        return QueuedJob::QUEUED;
    }

    public function setup()
    {
        // no-op
    }

    public function process()
    {
        if ($this->promoID && ($promo = Promo::get()->byID($this->promoID))) {
            // Only unpublish if it’s actually expired (safety check)
            if (method_exists($promo, 'Active') && !$promo->Active()) {
                if (method_exists($promo, 'doUnpublish')) {
                    $promo->doUnpublish();
                }
            }
        }
        $this->isComplete = true;
    }
}
