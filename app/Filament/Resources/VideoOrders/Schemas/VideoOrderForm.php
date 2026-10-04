<?php

namespace App\Filament\Resources\VideoOrders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Fulfilment workflow')->description('Pending → Confirmed → Processing → Delivered. Set the estimated date and the customer sees it on the tracking page.')
                ->schema([
                    TextInput::make('tracking_id')->label('Tracking ID')->disabled()->dehydrated(false),
                    Select::make('status')->options([
                        'new' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'in_production' => 'Processing',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])->default('new')->required(),
                    DateTimePicker::make('estimated_delivery_at')->label('Estimated delivery'),
                    TextInput::make('lemon_order_id')->label('Lemon order ID')->disabled()->dehydrated(false),
                    TextInput::make('delivery_url')->label('Finished file link (Drive / Dropbox / CDN)')
                        ->url()->maxLength(500)->columnSpanFull()
                        ->helperText('Pasted into the “delivered” email and shown as the download button on the tracking page.'),
                ])->columns(2),
            Section::make('Brief')->schema([
                TextInput::make('format_label')->label('Product / template'),
                TextInput::make('product_slug'),
                TextInput::make('customer_name')->required(),
                TextInput::make('customer_email')->required(),
                TextInput::make('recipient_name'),
                TextInput::make('recipient_email')->email(),
                Select::make('deliver_to')->options(['self' => 'Buyer', 'other' => 'Someone else']),
                TextInput::make('occasion'),
                Textarea::make('message')->required()->columnSpanFull(),
                Textarea::make('notes')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
