<?php

namespace App\Filament\Resources\VideoOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VideoOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_id')->label('Tracking')->searchable()->copyable(),
                TextColumn::make('customer_name')->searchable(),
                TextColumn::make('customer_email')->searchable()->toggleable(),
                TextColumn::make('format_label')->label('Product')->toggleable(),
                TextColumn::make('status')->badge()->sortable()->formatStateUsing(fn ($state) => match ($state instanceof \BackedEnum ? $state->value : $state) {
                    'new' => 'Pending', 'confirmed' => 'Confirmed',
                    'in_production' => 'Processing', 'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled', default => $state,
                }),
                TextColumn::make('estimated_delivery_at')->label('ETA')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
