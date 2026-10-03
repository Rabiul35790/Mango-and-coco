<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product')->schema([
                Select::make('category_id')->relationship('category', 'name')
                    ->required()->searchable()->preload()
                    ->helperText('Admin-created categories drive the frontend flow via slug.'),
                TextInput::make('name')->required()->maxLength(160),
                TextInput::make('slug')->required()->maxLength(120)->unique(ignoreRecord: true),
                TextInput::make('tagline')->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Select::make('kind')->options([
                    'sticker_pack' => 'Sticker Pack',
                    'video' => 'Custom Video',
                    'coloring_book' => 'Coloring Book',
                    'ecard' => 'eCard',
                ])->required(),
                Select::make('delivery_type')->options([
                    'instant' => 'Instant download (stickers / wallpaper)',
                    'custom' => 'Custom-made (video / egift-card wizard)',
                ])->required()->default('instant')->reactive(),
                TextInput::make('price_cents')->numeric()->required()->label('Price (cents)'),
                TextInput::make('currency')->default('USD')->maxLength(3),
                TextInput::make('image_key')->label('Fallback cover key (only if no upload above)')->maxLength(80)->default('hero')
                    ->helperText('hero, sticker-pack-1, sticker-pack-2, ecard.'),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
            ])->columns(2),

            Section::make('Card + details imagery')->description('Uploads win over the fallback key above. Card shows the cover; details page shows the gallery with arrows + counter.')
                ->schema([
                    FileUpload::make('cover_image')->label('Card / cover image')
                        ->disk('public')->directory('product-covers')->image()
                        ->maxSize(10240)->columnSpanFull(),
                    FileUpload::make('gallery')->label('Details gallery images')
                        ->disk('public')->directory('product-gallery')->image()
                        ->multiple()->maxFiles(8)->maxSize(10240)->columnSpanFull()
                        ->helperText('First gallery image doubles as the details-page side visual when there is no video banner.'),
                ])->columns(2),

            Section::make('Details hero video')->description('One separate video shown as the half-height banner on this product’s details page. This is NOT the buyer template chooser — templates are added below.')
                ->schema([
                    TextInput::make('demo_video_url')->label('Hero video URL (mp4)')->maxLength(500)->columnSpanFull()
                        ->helperText('Paste an mp4 URL — or upload a file below. Upload wins if both are set.'),
                    FileUpload::make('demo_video_file')->label('…or upload hero video file')
                        ->disk('public')->directory('product-demos')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(204800)->columnSpanFull()
                        ->helperText('Empty = still cover image banner instead. eGift cards always show the still image, never video.'),
                    TextInput::make('demo_video_poster')->label('Hero video poster image')->maxLength(500)->columnSpanFull(),
                    Textarea::make('instructions')->rows(4)->columnSpanFull(),
                    Repeater::make('usage_steps')->schema([
                        TextInput::make('step')->required(),
                    ])->columns(1)->columnSpanFull(),
                    Textarea::make('whats_included')->rows(3)->columnSpanFull(),
                    TextInput::make('meta_title')->maxLength(255),
                    TextInput::make('meta_description')->maxLength(255),
                ])->columns(2),

            Section::make('Templates — what the buyer picks in the wizard')->description('One row per template (Birthday, Congratulations…). VIDEO products: upload an mp4 per row. EGIFT products: upload a card image per row. Stickers / wallpaper / coloring books: leave empty, templates are ignored for them.')
                ->schema([
                    Repeater::make('templates')->relationship('templates')->columnSpanFull()
                        ->addActionLabel('Add template')
                        ->schema([
                            TextInput::make('label')->required()->maxLength(120)
                                ->placeholder('Birthday'),
                            FileUpload::make('file')->label('Template file (mp4 for video, image for eGift)')
                                ->disk('public')->directory('product-templates')
                                ->acceptedFileTypes(['video/mp4', 'video/webm', 'image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(204800)->required()->columnSpanFull(),
                            FileUpload::make('poster')->label('Thumbnail poster (video templates)')
                                ->disk('public')->directory('product-templates')->image()
                                ->maxSize(10240),
                            TextInput::make('sort_order')->label('Order')->numeric()->default(0),
                        ])->columns(2),
                ]),

            Section::make('Deliverable file (Cloudflare R2)')->description('Upload the ZIP here — it goes to the R2 bucket. Buyers get a 15-min signed URL after paid order.')
                ->schema([
                    Select::make('download_disk')->options(['r2' => 'Cloudflare R2', 'public' => 'Local public (dev only)'])->default('r2'),
                    FileUpload::make('download_path')->label('ZIP file')
                        ->disk(fn ($get) => $get('download_disk') ?: 'r2')
                        ->directory('deliverables')->acceptedFileTypes(['application/zip'])
                        ->maxSize(512000)->columnSpanFull(),
                    TextInput::make('lemon_variant_id')->label('Lemon Variant ID')->maxLength(60),
                    TextInput::make('lemon_product_id')->label('Lemon Product ID')->maxLength(60),
                ])->columns(2),
        ]);
    }
}
