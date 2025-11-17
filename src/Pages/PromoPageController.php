<?php
namespace Antlion\Promotionals\Pages;


use Antlion\Promotionals\Model\Promo;
use SilverStripe\ORM\DataList;
use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Security\SecurityToken;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\ORM\ValidationResult;
use App\Models\FormSubmission;


class PromoPageController extends PageController
{
    private static $allowed_actions = [
        'show',
        'PromoForm',
        'doSubmitPromoForm',
    ];

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

    /**
     * Resolve the current promo (for detail route like /promos/my-slug)
     */
    protected function currentPromo(): ?Promo
    {
        // 1) primary: the route param on the show action
        $slug = $this->getRequest()->param('Slug');

        // 2) fallback: hidden field on POST (useful on form submit routes)
        if (!$slug) {
            $slug = $this->getRequest()->postVar('PromoSlug');
        }

        return $slug ? Promo::get()->filter('URLSegment', $slug)->first() : null;
    }

    public function PromoForm(): Form
    {
        $promo = $this->currentPromo();

        $fields = FieldList::create(
            TextField::create('Name', 'Your name')->setAttribute('autocomplete', 'name'),
            TextField::create('Email', 'Email')->setAttribute('autocomplete', 'email'),
            TextField::create('Phone', 'Phone (optional)')->setAttribute('autocomplete', 'tel'),
            TextareaField::create('Message', 'Message')->setRows(5),
            // context (hidden)
            HiddenField::create('PromoSlug', '')->setValue($promo?->URLSegment ?? ''),
            HiddenField::create('PromoTitle', '')->setValue($promo?->Title ?? ''),
            HiddenField::create('PromoLink', '')->setValue($promo?->Link() ?? $this->Link())
        );

        $actions = FieldList::create(
            FormAction::create('doSubmitPromoForm', 'Send')
                ->setUseButtonTag(true)
                ->addExtraClass('button primary')
        );

        $form = Form::create($this, 'PromoForm', $fields, $actions, RequiredFields::create(['Name','Email','Message']));
        $form->setAttribute('novalidate', true);
        if (method_exists($form, 'enableSpamProtection')) {
            $form->enableSpamProtection();
        }
        return $form;
    }


    public function doSubmitPromoForm(array $data, Form $form)
    {
        if (empty($data['Name']) || empty($data['Email']) || empty($data['Message'])) {
            $form->sessionMessage('Please complete the required fields.', ValidationResult::TYPE_ERROR);
            return $this->redirectBack();
        }

        $promo = $this->currentPromo();

        // Work out who the email is going to (same logic as before)
        $site   = SiteConfig::current_site_config();
        $to     = $this->data()->MailTo ?: ($site->SupportEmail ?? null) ?: 'webmaster@localhost';
        $sentTo = is_array($to) ? implode(', ', $to) : (string) $to;

        // Build context values
        $promoTitle = $data['PromoTitle'] ?? ($promo?->Title ?? '');
        $promoLink  = $data['PromoLink'] ?? ($promo?->Link() ?? $this->Link());

        // Create generic submission record
        $submission = FormSubmission::create([
            'FormName'       => 'Promo enquiry',
            'FormAction'     => 'PromoForm',
            'PageID'         => $this->ID,
            'Context'        => $promoTitle,
            'ContextLink'    => $promoLink,
            'SubmitterName'  => $data['Name'] ?? '',
            'SubmitterEmail' => $data['Email'] ?? '',
            'SubmitterPhone' => $data['Phone'] ?? '',
            'Message'        => $data['Message'] ?? '',
            'SentTo'         => $sentTo,
            'RawData'        => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
        $submission->write();

        // Email subject/body same as before
        $prefix = $this->data()->FormSubjectPrefix ?: '[Promo Enquiry]';
        $subject = sprintf(
            '%s %s',
            $prefix,
            $promoTitle ? ('- ' . $promoTitle) : ('from ' . $submission->SubmitterName)
        );

        $body = <<<HTML
    <p><strong>Promo enquiry</strong></p>
    <p><strong>Promo:</strong> {$promoTitle}<br>
    <strong>Link:</strong> {$promoLink}</p>
    <p><strong>Name:</strong> {$submission->SubmitterName}<br>
    <strong>Email:</strong> {$submission->SubmitterEmail}<br>
    <strong>Phone:</strong> {$submission->SubmitterPhone}</p>
    <p><strong>Message:</strong><br>
    <pre style="white-space:pre-wrap; font-family:inherit">{$submission->Message}</pre></p>
    HTML;

        $email = Email::create()
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body);

        if (!empty($submission->SubmitterEmail)) {
            $email->setReplyTo($submission->SubmitterEmail);
        }

        try {
            $email->send();
            $submission->Status = 'Emailed';
            $submission->write();

            $form->sessionMessage('Thanks—your enquiry has been sent.', ValidationResult::TYPE_GOOD);
        } catch (\Throwable $e) {
            $submission->Status       = 'Error';
            $submission->ErrorMessage = $e->getMessage();
            $submission->write();

            $form->sessionMessage('Sorry, we could not send your message right now.', ValidationResult::TYPE_ERROR);
        }

        return $this->redirectBack();
    }


}
