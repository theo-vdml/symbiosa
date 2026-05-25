<?php

namespace App\Http\Controllers;

use App\Models\ContactPage;
use App\Settings\ContactSettings;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function __invoke(ContactSettings $settings)
    {
        $contactPage = ContactPage::first();

        return Inertia::render('Contact', [
            'settings' => [
                'heading' => $contactPage?->heading,
                'subheading' => $contactPage?->subheading,
                'description' => $contactPage?->description,
                'faq_heading' => $contactPage?->faq_heading,
                'faq_description' => $contactPage?->faq_description,
                'faq_items' => $contactPage?->faq_items ?? [],
                'email_heading' => $contactPage?->email_heading,
                'email_options' => $settings->email_options ?? [],
            ],
            'seo' => $contactPage?->getSeoData() ?? [],
        ]);
    }
}
