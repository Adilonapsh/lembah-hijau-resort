<?php

namespace App\Filament\Admin\Resources\RoomReportHistoryResource\Pages;

use App\Filament\Admin\Resources\RoomReportHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomReportHistories extends ListRecords
{
    protected static string $resource = RoomReportHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
