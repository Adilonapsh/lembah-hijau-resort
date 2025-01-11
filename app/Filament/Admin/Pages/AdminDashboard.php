<?php

namespace App\Filament\Admin\Pages;

use App\Models\Guests;
use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.admindashboard';

    public $data = [];

    public function mount() {
        $this->data["total_data_tamu"] = Guests::count();
        $this->data["total_kamar_terisi"] = 1000;
    }
}
