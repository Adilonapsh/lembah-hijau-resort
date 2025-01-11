<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoomStatusResource\Pages;
use App\Models\Room;
use App\Models\RoomReportHistory;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->with(['guests' => function ($query) {
                        $query->select('id', 'nama', 'id_kamar');
                    }])
                    ->withCount('guests');
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
                    }),
                TextColumn::make('kapasitas')
                    ->label('Jumlah Bed'),
                TextColumn::make('tersisa')
                    ->label('Sisa Bed')
                    ->getStateUsing(function ($record) {
                        return $record->kapasitas - $record->guests_count;
                    }),
                TextColumn::make('nama_tamu')
                    ->label('Nama Tamu')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        return $record->guests->pluck('nama');
                    }),
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
