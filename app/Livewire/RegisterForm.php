<?php

namespace App\Livewire;

use App\Models\Guests;
use App\Models\Kelas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class RegisterForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                Radio::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki-laki' => 'Laki-Laki',
                        'perempuan' => 'Perempuan',
                    ])
                    ->required(),
                TextInput::make('kantor_cabang')
                    ->label('Kantor Cabang')
                    ->required(),
                Select::make('id_kelas')
                    ->label('Pendidikan/Kelas')
                    ->options(Kelas::pluck('nama_kelas', 'id'))
                    ->required(),
                TextInput::make('batch')
                    ->label('Batch')
                    ->required(),
                Select::make('kendaraan')
                    ->label('Kendaraan')
                    ->options([
                        'Mobil' => 'Mobil',
                        'Motor' => 'Motor',
                        'Kendaraan Umum' => 'Kendaraan Umum',
                    ]),
                TextInput::make('no_polisi')
                    ->label('No Polisi Kendaraan')
                    ->required(),
                TextInput::make('no_hp')
                    ->label('Nomor Handphone'),
                TextInput::make('email')
                    ->label('Email'),
                DatePicker::make('tanggal_rencana_checkin')
                    ->label('Tanggal Checkin')
                    ->required(),
                DatePicker::make('tanggal_rencana_checkout')
                    ->label('Tanggal Checkout')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            Guests::create($this->form->getState());
            Notification::make()
                ->title('Berhasil')
                ->icon('heroicon-o-chart-bar')
                ->body('Data Sudah Dibuat')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal')
                ->icon('heroicon-o-chart-bar')
                ->body('Data Gagal Dibuat : ' . $e->getMessage())
                ->warning()
                ->send();
        }

        // dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.register-form');
    }
}
