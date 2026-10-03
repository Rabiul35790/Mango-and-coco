<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->schema([
                TextInput::make('tracking_id')->label('Tracking ID')->disabled()->dehydrated(false),
                Select::make('status')->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])->required(),
                DateTimePicker::make('estimated_delivery_at')->label('Estimated delivery'),
                TextInput::make('product_slug')->disabled()->dehydrated(false),
                TextInput::make('customer_name')->disabled()->dehydrated(false),
                TextInput::make('customer_email')->disabled()->dehydrated(false),
                TextInput::make('lemon_order_id')->label('Lemon order ID')->disabled()->dehydrated(false),
            ])->columns(2),
        ]);
    }
}
