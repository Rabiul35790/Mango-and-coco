<?php

namespace App\Filament\Pages;

use App\Actions\Catalog\GetActiveProducts;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Site settings';

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')->schema([
                TextInput::make('site_name')->required()->maxLength(160),
                FileUpload::make('logo_path')->label('Logo')->image()->directory('brand')->disk('public')->visibility('public'),
                Textarea::make('address')->rows(2)->columnSpanFull(),
                TextInput::make('contact_email')->email()->maxLength(200),
                TextInput::make('support_email')->email()->maxLength(200),
                Textarea::make('copyright_text')->rows(2)->columnSpanFull()
                    ->helperText('Use {year} for the current year.'),
            ])->columns(2),
            Section::make('Homepage hero — full-screen video')->description('100vh video hero on /. Upload an mp4 (landscape, muted loop, ideally < 15 MB, 720p). Empty = elegant poster fallback until you upload.')
                ->schema([
                    FileUpload::make('hero_video')->label('Hero video (mp4 / webm)')
                        ->disk('public')->directory('site-hero')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(204800)->columnSpanFull(),
                    FileUpload::make('hero_poster')->label('Hero poster (shown while video loads)')
                        ->disk('public')->directory('site-hero')->image()->maxSize(10240),
                    TextInput::make('hero_heading')->maxLength(255)->columnSpanFull()
                        ->helperText('Empty = default headline.'),
                    Textarea::make('hero_subheading')->rows(2)->columnSpanFull(),
                    TextInput::make('hero_cta_label')->maxLength(120),
                    TextInput::make('hero_cta_url')->maxLength(500),
                    TextInput::make('hero_secondary_cta_label')->label('Secondary CTA label')->maxLength(120),
                    TextInput::make('hero_secondary_cta_url')->label('Secondary CTA URL')->maxLength(500),
                ])->columns(2),
            Section::make('Tracking & pixels')->description('IDs are wired into the frontend layout automatically. Extra scripts are printed verbatim.')
                ->schema([
                    TextInput::make('meta_pixel_id')->label('Meta Pixel ID')->maxLength(60),
                    TextInput::make('tiktok_pixel_id')->label('TikTok Pixel ID')->maxLength(60),
                    TextInput::make('google_analytics_id')->label('Google Analytics / GA4 ID')->maxLength(60),
                    TextInput::make('microsoft_clarity_id')->label('Microsoft Clarity ID')->maxLength(60),
                    Textarea::make('extra_head_scripts')->label('Extra <head> scripts')->rows(4)->columnSpanFull()
                        ->helperText('Pasted inside <head>. Include your own <script> tags.'),
                    Textarea::make('extra_body_scripts')->label('Extra scripts before </body>')->rows(4)->columnSpanFull(),
                ])->columns(2),
        ])->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('Save settings')->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::current()->update($data);
        GetActiveProducts::flush();

        Notification::make()->title('Settings saved')->success()->send();
    }
}
