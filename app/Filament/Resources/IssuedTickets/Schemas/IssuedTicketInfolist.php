<?php

namespace App\Filament\Resources\IssuedTickets\Schemas;

use App\Filament\Resources\Checkouts\CheckoutResource;
use App\Filament\Resources\Events\EventResource;
use App\Services\TicketPdfService;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IssuedTicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Validité')
                    ->compact()
                    ->schema([
                        // TextEntry::make('qr_code')
                        //     ->hiddenLabel()
                        //     ->state(function ($record) {
                        //         return QrCode::size(120)
                        //             ->style('round')
                        //             ->margin(1)
                        //             ->generate($record->public_id);
                        //     })
                        //     ->html()
                        //     ->extraAttributes(['class' => 'flex justify-center mb-6']),

                        // TextEntry::make('status_label')
                        //     ->hiddenLabel()
                        //     ->state(fn($record) => $record->checked_in_at ? 'SCANNÉ' : 'VALIDE')
                        //     ->color(fn($record) => $record->checked_in_at ? 'success' : 'info')
                        //     ->weight(FontWeight::ExtraBold)
                        //     ->alignCenter()
                        //     ->extraAttributes(fn($record) => [
                        //         'class' => 'p-4 rounded-xl border-2 text-center shadow-sm mb-4',
                        //         'style' => $record->checked_in_at
                        //             ? 'border-color: rgba(34, 197, 94, 0.4); background-color: rgba(34, 197, 94, 0.05);'
                        //             : 'border-color: rgba(59, 130, 246, 0.4); background-color: rgba(59, 130, 246, 0.05);',
                        //     ]),

                        Action::make('toggleCheckIn')
                            ->label(fn($record) => $record->checked_in_at ? 'Annuler le scan' : 'Valider le ticket')
                            ->icon(fn($record) => $record->checked_in_at ? 'heroicon-m-x-circle' : 'heroicon-m-check-badge')
                            ->color(fn($record) => $record->checked_in_at ? 'gray' : 'success')
                            ->size('lg')
                            ->requiresConfirmation()
                            ->extraAttributes(['class' => 'w-full justify-center'])
                            ->action(function ($record) {
                                $record->update([
                                    'checked_in_at' => $record->checked_in_at ? null : now(),
                                ]);
                            }),
                    ]),

                Section::make('Détails du Billet')
                    ->icon('heroicon-m-ticket')
                    ->headerActions([
                        Action::make('downloadPdf')
                            ->label('PDF')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->color('gray')
                            ->size('sm')
                            ->action(function ($record, TicketPdfService $pdfService) {
                                $pdf = $pdfService->generate($record->event, $record);
                                $filename = $record->public_id . '.pdf';

                                return response()->streamDownload(
                                    fn() => print($pdf->output()),
                                    $filename
                                );
                            })
                    ])
                    ->schema([
                        TextEntry::make('reservable.name')
                            ->label('Produit')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),

                        TextEntry::make('reservable.description')
                            ->label('Description')
                            ->size('sm')
                            ->color('gray')
                            ->placeholder('Aucune description supplémentaire.'),

                        TextEntry::make('public_id')
                            ->label('Code de sécurité')
                            ->fontFamily('mono')
                            ->copyable()
                            ->color('gray'),

                        // TextEntry::make('type')
                        //     ->label('Type de billet')
                        //     ->badge()
                        //     ->state(fn($record) => $record->reservable_type === 'App\Models\TicketType' ? 'Accès Événement' : 'Option / Supplément')
                        //     ->color(fn($record) => $record->reservable_type === 'App\Models\TicketType' ? 'primary' : 'warning'),

                        TextEntry::make('ticketPrice.name')
                            ->label('Tarif appliqué')
                            ->placeholder('Standard')
                            ->icon('heroicon-m-tag'),

                        TextEntry::make('price_paid')
                            ->label('Prix payé')
                            ->money('EUR', divideBy: 100)
                            ->weight(FontWeight::Bold)
                            ->color('success'),

                        TextEntry::make('checked_in_at')
                            ->label('Heure du scan')
                            ->dateTime('H:i:s (d/m/Y)')
                            ->icon('heroicon-m-qr-code')
                            ->visible(fn($record) => $record->checked_in_at !== null),
                    ]),

                Section::make('Événement')
                    ->icon('heroicon-m-calendar')
                    ->headerActions([
                        Action::make('viewEvent')
                            ->label('Voir')
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->color('gray')
                            ->size('sm')
                            ->url(fn($record) => EventResource::getUrl('edit', ['record' => $record->event])),
                    ])
                    ->schema([
                        TextEntry::make('event.title')
                            ->label('Nom')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('event.date')
                            ->label('Date')
                            ->date('l d F Y')
                            ->icon('heroicon-m-calendar-days'),

                        TextEntry::make('event.location')
                            ->label('Lieu')
                            ->icon('heroicon-m-map-pin')
                            ->placeholder('Non spécifié'),

                        TextEntry::make('event.address')
                            ->label('Adresse complète')
                            ->color('gray')
                            ->size('sm'),
                    ]),

                Section::make('Client & Commande')
                    ->icon('heroicon-m-shopping-bag')
                    ->headerActions([
                        Action::make('viewCheckout')
                            ->label('Voir')
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->color('gray')
                            ->size('sm')
                            ->url(fn($record) => CheckoutResource::getUrl('view', ['record' => $record->checkout])),
                    ])
                    ->schema([
                        TextEntry::make('checkout.customer_name')
                            ->label('Client')
                            ->weight(FontWeight::Bold)
                            ->placeholder('—'),

                        TextEntry::make('checkout.status')
                            ->label('Paiement')
                            ->badge(),

                        TextEntry::make('checkout.customer_email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextEntry::make('checkout.created_at')
                            ->label('Acheté le')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('checkout.uuid')
                            ->label('ID Commande')
                            ->fontFamily('mono')
                            ->size('xs')
                            ->color('gray')
                            ->copyable(),
                    ]),
            ]);
    }
}
