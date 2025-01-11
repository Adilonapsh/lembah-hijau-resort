<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kamar';

    protected static ?string $modelLabel = 'Kamar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('unit')
                    ->label('Unit')
                    ->options([
                        'Bandung' => 'Bandung',
                        'Surabaya' => 'Surabaya',
                    ])
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('nama')
                    ->label('Nama Kamar')
                    ->required()
                    ->placeholder('Masukkan Nama Kamar')
                    ->columnSpanFull(),
                TextInput::make('tipe')
                    ->label('Tipe Kamar')
                    ->required()
                    ->placeholder('Masukkan Tipe Kamar')
                    ->columnSpanFull(),
                TextInput::make('harga')
                    ->label('Harga Kamar')
                    ->required()
                    ->placeholder('Masukkan Harga Kamar')
                    ->numeric()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status Kamar')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'tidak tersedia' => 'Tidak Tersedia',
                    ])
                    ->placeholder('Masukkan Status Kamar')
                    ->columnSpanFull()
                    ->required()
                    ->default('tersedia'),
                TextInput::make('kapasitas')
                    ->label('Kapasitas Kamar')
                    ->required()
                    ->placeholder('Masukkan Kapasitas Kamar')
                    ->numeric()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit Kamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe Kamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga Kamar')
                    ->numeric()
                    ->money('IDR', true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Kamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kapasitas Kamar')
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
