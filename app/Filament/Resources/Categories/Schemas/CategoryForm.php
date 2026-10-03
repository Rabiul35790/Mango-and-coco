<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')->description('Slug drives frontend flow: stickers|wallpaper|coloring-books = details+download, video|egift-card = wizard.')->schema([
                TextInput::make('name')->required()->maxLength(160),
                TextInput::make('slug')->required()->maxLength(80)->unique(ignoreRecord: true)
                    ->helperText('Lowercase, e.g. stickers, wallpaper, video, egift-card. Used in /shop/{slug}.'),
                TextInput::make('tagline')->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TextInput::make('hero_image')->label('Hero image URL')->maxLength(255),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
                TextInput::make('meta_title')->maxLength(255),
                TextInput::make('meta_description')->maxLength(255),
                TextInput::make('details_heading')->maxLength(255),
                Textarea::make('details_body')->rows(5)->columnSpanFull()
                    ->helperText('Shown on listing + details pages (e.g. WhatsApp/Telegram instructions).'),
            ])->columns(2),
            Section::make('Hero — full-screen video')->description('100vh video hero for /shop/{slug}. Upload an mp4 (landscape, muted loop, ideally < 15 MB, 720p). Empty = elegant poster fallback until you upload.')
                ->schema([
                    FileUpload::make('hero_video')->label('Hero video (mp4 / webm)')
                        ->disk('public')->directory('category-heroes')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(204800)->columnSpanFull(),
                    FileUpload::make('hero_poster')->label('Hero poster (shown while video loads)')
                        ->disk('public')->directory('category-heroes')->image()->maxSize(10240),
                    TextInput::make('hero_image')->label('Fallback hero image URL')->maxLength(255),
                    TextInput::make('hero_heading')->maxLength(255)->columnSpanFull()
                        ->helperText('Empty = category name.'),
                    Textarea::make('hero_subheading')->rows(2)->columnSpanFull()
                        ->helperText('Empty = category tagline.'),
                    TextInput::make('hero_cta_label')->maxLength(120)->placeholder('Shop the collection'),
                    TextInput::make('hero_cta_url')->maxLength(500)->placeholder('#products'),
                ])->columns(2),
        ]);
    }
}
