<?php

use App\Settings\PageSettingsMigration;

return new class extends PageSettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('contact.heading', 'Contact');
        $this->migrator->add('contact.subheading', 'Nous contacter');
        $this->migrator->add('contact.description', 'Une question, une idée ou un projet ? Contactez-nous directement par e-mail et notre équipe reviendra vers vous.');

        $this->migrator->add('contact.faq_heading', 'Questions Fréquentes');
        $this->migrator->add('contact.faq_description', 'La réponse à votre question se trouve peut-être déjà ici.');
        $this->migrator->add('contact.faq_items', []);

        $this->migrator->add('contact.email_heading', 'Nous contacter par e-mail');
        $this->migrator->add('contact.email_options', [
            [
                'label' => 'Une question ?',
                'email' => 'hi@symbiosa.be',
            ],
            [
                'label' => 'Devenir sponsor ?',
                'email' => 'sponsors@symbiosa.be',
            ],
            [
                'label' => 'Devenir bénévole ?',
                'email' => 'team@symbiosa.be',
            ]
        ]);

        $this->addSeoSettings('contact');
    }

    public function down(): void
    {
        $this->migrator->delete('contact.heading');
        $this->migrator->delete('contact.subheading');
        $this->migrator->delete('contact.description');
        $this->migrator->delete('contact.faq_heading');
        $this->migrator->delete('contact.faq_description');
        $this->migrator->delete('contact.faq_items');
        $this->migrator->delete('contact.email_heading');
        $this->migrator->delete('contact.email_options');
        $this->deleteSeoSettings('contact');
    }
};
