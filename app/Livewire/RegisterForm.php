<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\PasswordInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;

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
                Select::make('pendidikan_kelas')
                    ->label('Pendidikan/Kelas')
                    ->options([
                        'BSDP 0 FOR FRONTLINER BATCH - 01/2025' => 'BSDP 0 FOR FRONTLINER BATCH - 01/2025',
                        'BSDP 0 FOR FRONTLINER BATCH - 02/2025' => 'BSDP 0 FOR FRONTLINER BATCH - 02/2025',
                    ]),
                TextInput::make('Batch')
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
                DatePicker::make('tanggal_checkin')
                    ->label('Tanggal Checkin')
                    ->required(),
                DatePicker::make('tanggal_checkout')
                    ->label('Tanggal Checkout')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }


    public function render()
    {
        return view('livewire.register-form');
    }
}
