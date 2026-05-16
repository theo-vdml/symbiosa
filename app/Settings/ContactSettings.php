<?php

namespace App\Settings;

class ContactSettings extends PageSettings
{
    public ?string $heading;
    public ?string $subheading;
    public ?string $description;

    public ?string $faq_heading;
    public ?string $faq_description;
    public ?array $faq_items;

    public ?string $email_heading;
    public ?array $email_options;

    public static function group(): string
    {
        return 'contact';
    }

    public function getSeoDefaults(): array
    {
        return [
            'title' => 'Symbiosa - Contact',
            'description' => 'Une question, une idée ou un projet ? Contactez-nous directement par e-mail.',
        ];
    }
}
