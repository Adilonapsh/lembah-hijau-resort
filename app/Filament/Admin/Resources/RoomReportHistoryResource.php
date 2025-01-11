<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoomReportHistoryResource\Pages;
use App\Filament\Admin\Resources\RoomReportHistoryResource\RelationManagers;
use App\Models\RoomReportHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomReportHistoryResource extends Resource
{
    protected static ?string $model = RoomReportHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_id')
                    ->label('Nama Kamar')
                    ->getStateUsing(function ($record) {
                        // dd($record->room->first()->nama);
                        return $record->room->first()->nama;
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Laporan')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListRoomReportHistories::route('/'),
            'create' => Pages\CreateRoomReportHistory::route('/create'),
            'edit' => Pages\EditRoomReportHistory::route('/{record}/edit'),
        ];
    }
}
