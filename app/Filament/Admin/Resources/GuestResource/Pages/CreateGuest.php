<?php

namespace App\Filament\Admin\Resources\GuestResource\Pages;

use App\Filament\Admin\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;
}
