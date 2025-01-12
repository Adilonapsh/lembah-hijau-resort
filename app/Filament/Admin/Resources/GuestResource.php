<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GuestResource\Pages;
use App\Models\Guests;
use App\Models\Kelas;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GuestResource extends Resource
{
    protected static ?string $model = Guests::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tamu';

    protected static ?string $modelLabel = 'Tamu';

    protected static ?int $navigationSort = -1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->placeholder('Masukkan Nama')
                    ->columnSpanFull(),
                Grid::make()->columns(3)->schema([
                    Radio::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options([
                            'laki-laki' => 'Laki-Laki',
                            'perempuan' => 'Perempuan',
                        ])
                        ->required(),
                    TextInput::make('no_hp')
                        ->label('Nomor Handphone')
                        ->placeholder('Masukkan Nomor Handphone'),
                    TextInput::make('email')
                        ->label('Email')
                        ->placeholder('Masukkan Email'),
                    TextInput::make('kantor_cabang')
                        ->label('Kantor Cabang')
                        ->required(),
                    Select::make('pendidikan_kelas')
                        ->label('Pendidikan/Kelas')
                        ->options([
                            'BSDP 0 FOR FRONTLINER BATCH - 01/2025' => 'BSDP 0 FOR FRONTLINER BATCH - 01/2025',
                            'BSDP 0 FOR FRONTLINER BATCH - 02/2025' => 'BSDP 0 FOR FRONTLINER BATCH - 02/2025',
                        ]),
                    TextInput::make('batch')
                        ->label('Batch')
                        ->required(),
                ]),
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
                DatePicker::make('tanggal_rencana_checkin')
                    ->label('Tanggal Checkin')
                    ->required()
                    ->readOnly(function ($operation) {
                        return $operation == 'edit' ? true : false;
                    }),
                DatePicker::make('tanggal_rencana_checkout')
                    ->label('Tanggal Checkout')
                    ->required()
                    ->readOnly(function ($operation) {
                        return $operation == 'edit' ? true : false;
                    }),
                Select::make('nama_kamar')
                    ->label('Nama Kamar')
                    ->options(function () {
                        return Room::all()->pluck('nama', 'id');
                    })
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_kamar')
                    ->label('Nama Kamar')
                    ->getStateUsing(function ($record) {
                        if ($record->id_kamar == null) {
                            return '-';
                        }
                        return Room::find($record->id_kamar)->nama;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kantor_cabang')
                    ->label('Kantor Cabang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id_kelas')
                    ->label('Kelas')
                    ->getStateUsing(function ($record) {
                        return $record->kelas ? $record->kelas->nama_kelas : '-';
                    }),
                TextColumn::make('batch')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kendaraan')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_polisi')
                    ->label('No Polisi Kendaraan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_hp')
                    ->label('Nomor Handphone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_checkin')
                    ->label('Tanggal Checkin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_checkout')
                    ->label('Tanggal Checkout')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'laki-laki' => 'Laki-Laki',
                        'perempuan' => 'Perempuan',
                    ]),
                SelectFilter::make('kendaraan')
                    ->label('Kendaraan')
                    ->options([
                        'Mobil' => 'Mobil',
                        'Motor' => 'Motor',
                        'Kendaraan Umum' => 'Kendaraan Umum',
                    ]),
                SelectFilter::make('id_kelas')
                    ->label('Kelas')
                    ->options(Kelas::pluck('nama_kelas', 'id')),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('checkin'),
                        DatePicker::make('checkout'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['checkin'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['checkout'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('pilih_kamar')
                    ->label('Pilih Kamar')
                    ->icon('heroicon-o-document-plus')
                    ->action(fn(Collection $records, array $data) => static::processBulkAction($records, $data))
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('id_kamar')
                            ->label('Nama Kamar')
                            ->options(function () {
                                return Room::all()
                                    ->pluck('nama', 'id')
                                    ->prepend('Pilih Ruangan', '');
                            })
                            ->searchable(),
                    ]),
                Tables\Actions\BulkAction::make('Checkin')
                    ->label('Checkin')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'tanggal_checkin' => now(),
                            ]);
                        }
                        Notification::make()
                            ->title(count($records) . ' Tamu berhasil checkin')
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\BulkAction::make('checkout')
                    ->label('Checkout')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('danger')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'tanggal_checkout' => now(),
                            ]);
                        }
                        Notification::make(count($records) . ' Tamu berhasil checkout')
                            ->title(count($records) . ' Tamu berhasil checkout')
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->success()
                            ->send();
                    }),
            ])
            ->deselectAllRecordsWhenFiltered(false);
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }

    public static function processBulkAction($records, $data)
    {
        foreach ($records as $record) {
            $record->update([
                'id_kamar' => $data['id_kamar'],
            ]);
        }
        Notification::make()
            ->title(count($records) . ' Tamu berhasil di set kamar')
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }
}
