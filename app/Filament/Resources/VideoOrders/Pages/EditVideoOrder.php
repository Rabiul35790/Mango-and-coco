<?php

namespace App\Filament\Resources\VideoOrders\Pages;

use App\Filament\Resources\VideoOrders\VideoOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVideoOrder extends EditRecord
{
    protected static string $resource = VideoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
