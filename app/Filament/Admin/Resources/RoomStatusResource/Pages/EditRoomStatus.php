<?php

namespace App\Filament\Admin\Resources\RoomStatusResource\Pages;

use App\Filament\Admin\Resources\RoomStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomStatus extends EditRecord
{
    protected static string $resource = RoomStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
