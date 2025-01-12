<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoomStatusResource\Pages;
use App\Models\Kelas;
use App\Models\Room;
use App\Models\RoomReportHistory;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoomStatusResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Status Kamar';

    protected static ?string $modelLabel = 'Status Kamar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Room::query()
                    ->select(
                        'rooms.*',
                        DB::raw('MIN(guests.tanggal_checkin) as tanggal_checkin_awal'),
                        DB::raw('MAX(guests.tanggal_checkout) as tanggal_checkout_terakhir'),
                        DB::raw('(rooms.kapasitas - COUNT(guests.id)) as tersisa'),
                    )
                    ->leftJoin('guests', function ($join) {
                        $join->on('rooms.id', '=', 'guests.id_kamar')
                            ->whereNotNull('guests.tanggal_checkin')
                            ->whereNull('guests.tanggal_checkout');
                    })
                    ->groupBy('rooms.id')
                    ->with(['guests' => function ($query) {
                        $query->select(
                            'id',
                            'nama',
                            'id_kamar',
                            'id_kelas',
                            'batch',
                            'tanggal_checkin',
                            'tanggal_checkout'
                        )
                            ->whereNotNull('guests.tanggal_checkin')
                            ->whereNull('guests.tanggal_checkout')
                            ->orderBy('tanggal_checkin');
                    }])
                    ->withCount(['guests' => function ($query) {
                        $query->whereNotNull('tanggal_checkin')
                            ->whereNull('tanggal_checkout');
                    }]);
            })
            ->columns([
                TextColumn::make('tipe')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama Kamar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guests_count')
                    ->label('Bed Terisi')
                    ->getStateUsing(function ($record) {
                        return $record->guests_count ?? 0;
                    })
                    ->summarize(
                        Sum::make()->label('Total Bed Terisi')->query(fn(QueryBuilder $query) => $query->selectRaw('SUM(CAST(guests_count AS INTEGER)) as total_beds'))
                    )
                    ->alignCenter(),
                TextColumn::make('kapasitas')
                    ->label('Jumlah Bed')
                    ->summarize(
                        Sum::make()->label('Total Jumlah Bed')->query(fn(QueryBuilder $query) => $query->selectRaw('SUM(CAST(kapasitas AS INTEGER)) as kapasitas'))
                    )
                    ->alignCenter(),
                TextColumn::make('tersisa')
                    ->label('Sisa Bed')
                    ->getStateUsing(function ($record) {
                        return $record->kapasitas - $record->guests_count;
                    })
                    ->summarize(Sum::make()->label('Total Sisa Bed'))
                    ->alignCenter(),
                TextColumn::make('nama_tamu')
                    ->label('Nama Tamu')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->guests->pluck('nama');
                    }),
                TextColumn::make('id_kelas')
                    ->label('Kelas')
                    ->getStateUsing(function ($record) {
                        foreach ($record->guests as $guest) {
                            return Kelas::find($guest->id_kelas)->nama_kelas . " (" . $guest->batch . ")";
                        }
                        // return $record->kelas ? $record->kelas->nama_kelas : '-';
                    }),
                TextColumn::make('tanggal_checkin_awal')
                    ->label('Tanggal Checkin')
                    ->badge(),
                TextColumn::make('tanggal_checkout_terakhir')
                    ->label('Tanggal Checkout')
                    ->badge(),
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
            ])
            ->headerActions([
                Action::make('daily-report')
                    ->label('Daily Report')
                    ->icon('heroicon-o-chart-bar')
                    ->action(function () {
                        $data = Room::withCount('guests')->get();
                        $data->each(function ($room) {
                            $room->kapasitas = $room->kapasitas;
                            $room->tersisa = $room->kapasitas - $room->guests_count;
                            $room->nama_tamu = $room->guests->pluck('nama');
                        });
                        if (RoomReportHistory::where('created_at', '>=', now()->startOfDay())->exists()) {
                            Notification::make()
                                ->title('Daily Report')
                                ->icon('heroicon-o-chart-bar')
                                ->body('Daily Report Sudah Dibuat Hari Ini')
                                ->warning()
                                ->send();

                            return;
                        } else {
                            foreach ($data as $room) {
                                RoomReportHistory::create([
                                    'room_id' => $room->id,
                                    'user_id' => auth()->id(),
                                    'data_history' => $room->toArray(),
                                ]);
                            }
                            Notification::make()
                                ->title('Daily Report')
                                ->icon('heroicon-o-chart-bar')
                                ->body('Daily Report Berhasil Dibuat')
                                ->success()
                                ->send();
                        }
                    }),
                Action::make("occupancies")
                    ->label(function () {
                        $total_kamar = Room::count();
                        $kamar_terisi = Room::whereHas('guests', function ($query) {
                            $query->whereNotNull('tanggal_checkin')
                                ->whereNull('tanggal_checkout');
                        })->count();
                        $result = number_format(($kamar_terisi/$total_kamar * 100), 2);
                        return 'Occupancies : ' . $result . "%";
                    })
                    ->icon('heroicon-o-chart-bar')
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
            'index' => Pages\ListRoomStatuses::route('/'),
            // 'create' => Pages\CreateRoomStatus::route('/create'),
            // 'edit' => Pages\EditRoomStatus::route('/{record}/edit'),
        ];
    }
}
