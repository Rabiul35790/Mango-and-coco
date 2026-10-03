<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer')->schema([
                TextInput::make('name')->required()->maxLength(120),
                TextInput::make('email')->email()->required()->maxLength(200)->unique(ignoreRecord: true),
                TextInput::make('password')->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Leave blank to keep the current password when editing.'),
                Toggle::make('is_admin')->label('Admin (can open /admin)')
                    ->helperText('Customers must stay off — only admins open the panel.'),
            ])->columns(2),
        ]);
    }
}
