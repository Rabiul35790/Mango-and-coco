<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review')->schema([
                Select::make('product_id')->relationship('product', 'name')->required()->searchable()->preload(),
                Select::make('user_id')->relationship('user', 'email')->required()->searchable()->preload(),
                Select::make('rating')->options([1 => '1 ★', 2 => '2 ★', 3 => '3 ★', 4 => '4 ★', 5 => '5 ★'])->required(),
                Select::make('status')->options([
                    'pending' => 'Pending moderation',
                    'approved' => 'Approved (visible on site)',
                    'rejected' => 'Rejected (hidden)',
                ])->required()->default('pending'),
                TextInput::make('title')->maxLength(160)->columnSpanFull(),
                Textarea::make('body')->rows(4)->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
