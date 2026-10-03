<?php

namespace App\Filament\Resources\VideoOrders\Pages;

use App\Filament\Resources\VideoOrders\VideoOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoOrders extends ListRecords
{
    protected static string $resource = VideoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
