<?php

namespace App\Filament\Admin\Resources\GuestResource\Pages;

use App\Filament\Admin\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuest extends EditRecord
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
