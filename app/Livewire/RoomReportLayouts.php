<?php

namespace App\Livewire;

use App\Models\RoomReportHistory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;

class RoomReportLayouts extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(RoomReportHistory::query())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Laporan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room_id')
                    ->label('Nama Kamar')
                    ->getStateUsing(function ($record) {
                        // dd($record->room->first()->nama);
                        return $record->room->first()->nama;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guests_count')
                    ->label('Bed Terisi')
                    ->getStateUsing(function ($record) {
                        return $record->data_history['guests_count'] ?? 0;
                    }),
                TextColumn::make('kapasitas')
                    ->label('Jumlah Bed')
                    ->getStateUsing(function ($record) {
                        return $record->data_history['kapasitas'];
                    }),
                TextColumn::make('tersisa')
                    ->label('Sisa Bed')
                    ->getStateUsing(function ($record) {
                        return $record->data_history['kapasitas'] - $record->data_history['guests_count'];
                    }),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Tanggal Laporan')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.room-report-layouts');
    }
}
