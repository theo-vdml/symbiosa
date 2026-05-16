<?php

namespace App\Http\Controllers;

use App\Settings\ContactSettings;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function __invoke(ContactSettings $settings)
    {
        return Inertia::render('Contact', [
            'settings' => [
                'heading' => $settings->heading,
                'subheading' => $settings->subheading,
                'description' => $settings->description,
                'faq_heading' => $settings->faq_heading,
                'faq_description' => $settings->faq_description,
                'faq_items' => $settings->faq_items ?? [],
                'email_heading' => $settings->email_heading,
                'email_options' => $settings->email_options ?? [],
            ],
            'seo' => $settings->getSeoData(),
        ]);
    }
}
