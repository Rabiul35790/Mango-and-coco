<?php

namespace App\Filament\Resources\VideoOrders;

use App\Filament\Resources\VideoOrders\Pages\CreateVideoOrder;
use App\Filament\Resources\VideoOrders\Pages\EditVideoOrder;
use App\Filament\Resources\VideoOrders\Pages\ListVideoOrders;
use App\Filament\Resources\VideoOrders\Schemas\VideoOrderForm;
use App\Filament\Resources\VideoOrders\Tables\VideoOrdersTable;
use App\Models\VideoOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VideoOrderResource extends Resource
{
    protected static ?string $model = VideoOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return VideoOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVideoOrders::route('/'),
            'create' => CreateVideoOrder::route('/create'),
            'edit' => EditVideoOrder::route('/{record}/edit'),
        ];
    }
}
