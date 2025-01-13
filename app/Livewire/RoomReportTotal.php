<?php

namespace App\Livewire;

use App\Exports\RoomReportExport;
use App\Models\RoomReportHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class RoomReportTotal extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function getTableRecordKey($record): string
    {
        return (string) $record->date;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // $room = RoomReportHistory::query();
                // $room->select(
                //     DB::raw('DATE(created_at) as date'),
                //     DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'guests_count\'), \'0\') AS INTEGER)) as total_terisi'),
                //     DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'tersisa\'), \'0\') AS INTEGER)) as total_tersisa'),
                // )
                //     ->groupBy(DB::raw('DATE(created_at)'))
                //     ->orderBy('date', 'asc');
                // return $room;
                $room = RoomReportHistory::query();
                $room->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(CASE WHEN CAST(COALESCE(json_extract_path_text(data_history, \'guests_count\'), \'0\') AS INTEGER) > 0 THEN 1 ELSE 0 END) as kamar_terisi'),
                    DB::raw('SUM(CASE WHEN CAST(COALESCE(json_extract_path_text(data_history, \'guests_count\'), \'0\') AS INTEGER) = 0 THEN 1 ELSE 0 END) as kamar_kosong')
                )
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date', 'asc');
                return $room;
            })
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal Laporan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kamar_terisi')
                    ->label('Total Kamar Terisi'),
                TextColumn::make('kamar_kosong')
                    ->label('Total Kamar Kosong'),
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
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->headerActions([
                ActionsAction::make('export-excel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return Excel::download(new RoomReportExport, 'room_report_' . now() . '.xlsx');
                    }),
                // ActionsAction::make('export-pdf')
                //     ->label('Export to PDF')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->action(function () {
                //         $room = RoomReportHistory::query();
                //         $room->select(
                //             DB::raw('DATE(created_at) as date'),
                //             DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'guests_count\'), \'0\') AS INTEGER)) as total_terisi'),
                //             DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'tersisa\'), \'0\') AS INTEGER)) as total_tersisa'),
                //         )
                //             ->groupBy(DB::raw('DATE(created_at)'))
                //             ->orderBy('date', 'asc');

                //         $data = $room->get()->map(function ($item) {
                //             $item->date = preg_replace('/[^\x20-\x7E]/', '', $item->date);
                //             $item->total_tersisa = preg_replace('/[^\x20-\x7E]/', '', $item->total_tersisa);
                //             $item->total_terisi = preg_replace('/[^\x20-\x7E]/', '', $item->total_terisi);
                //             return $item;
                //         });
                //         $pdf = Pdf::loadView('pdf.room_report', compact('data'))
                //             ->setOption('isHtml5ParserEnabled', true)
                //             ->setOption('isPhpEnabled', true)
                //             ->setOption('isUtf8Enabled', true);
                //         return $pdf->download('room_report.pdf');
                //     }),
            ]);
    }

    public function render()
    {
        return view('livewire.room-report-total');
    }
}
