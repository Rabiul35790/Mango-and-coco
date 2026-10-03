<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->searchable()->sortable(),
                TextColumn::make('user.email')->searchable()->toggleable(),
                TextColumn::make('rating')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')->action(fn ($record) => $record->update(['status' => 'approved']))
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->requiresConfirmation(false),
                Action::make('reject')->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->requiresConfirmation(false),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
