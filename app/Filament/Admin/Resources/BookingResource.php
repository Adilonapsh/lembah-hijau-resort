<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Filament\Admin\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\Kelas;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_kelas')
                    ->label('Pendidikan/Kelas')
                    ->options(Kelas::pluck('nama_kelas', 'id'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make("jumlah_peserta")
                    ->label("Jumlah Peserta")
                    ->columnSpanFull(),
                Select::make("id_kamar")
                    ->label('Nama Kamar')
                    ->options(
                        Room::all()->mapWithKeys(function ($room) {
                            return [$room->id => "{$room->nama} ({$room->kapasitas})"];
                        })
                    )
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->columnSpanFull(),
                Select::make('unit')
                    ->label('Unit')
                    ->options([
                        'Bandung' => 'Bandung',
                        'Surabaya' => 'Surabaya',
                    ])
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tanggal_rencana_checkin')
                    ->label('Tanggal Checkin')
                    ->required(),
                DatePicker::make('tanggal_rencana_checkout')
                    ->label('Tanggal Checkout')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_kelas')
                    ->label('Pendidikan/Kelas')
                    ->getStateUsing(fn(Booking $record) => $record->kelas->nama_kelas),
                TextColumn::make('jumlah_peserta')
                    ->label('Jumlah Peserta'),
                TextColumn::make('id_kamar')
                    ->label('Nama Kamar')
                    ->getStateUsing(fn(Booking $record) => collect($record->id_kamar)->map(fn($room) => Room::find($room)->nama)),
                TextColumn::make('unit')
                    ->label('Unit'),
                TextColumn::make('tanggal_rencana_checkin')
                    ->label('Tanggal Checkin')
                    ->date(),
                TextColumn::make('tanggal_rencana_checkout')
                    ->label('Tanggal Checkout')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
