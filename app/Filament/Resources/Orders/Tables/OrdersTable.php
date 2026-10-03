<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_id')->label('Tracking')->searchable()->copyable(),
                TextColumn::make('product_slug')->label('Product')->searchable(),
                TextColumn::make('customer_email')->searchable()->toggleable(),
                TextColumn::make('total_cents')->label('Total')
                    ->formatStateUsing(fn ($state, $record) => number_format($state / 100, 2).' '.strtoupper($record->currency ?? 'USD')),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->label('Placed')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
