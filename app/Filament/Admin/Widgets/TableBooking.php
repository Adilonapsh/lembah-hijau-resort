<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\Room;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TableBooking extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
            )
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
            ]);
    }
}
