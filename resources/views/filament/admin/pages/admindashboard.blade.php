<x-filament-panels::page>

    <div class="flex flex-col gap-4">
        <div class="w-full flex flex-col justify-center items-center gap-2 bg-white"
            style="height:200px; color:black; border-radius: 0.75rem">
            <h1 class="text-3xl font-bold">Data Hari Ini </h1>
            <p class="fi-header-subheading text-lg">{{ \Carbon\Carbon::now()->toDayDateTimeString() }}</p>
        </div>
        <div class="">
            @livewire(\App\Filament\Admin\Widgets\RoomTotal::class)
        </div>
        <div class="grid gap-6 md:grid-cols-3" style="margin-top: 1rem">
            <div class="flex flex-col gap-2" style="height: 100px; color: black; border-radius: 0.75rem; gap: 1rem">
                <div class="text-center bg-white w-full p-4 rounded-lg">
                    <h1 class="text-3xl font-bold">Total Keseluruhan</h1>
                </div>
                <div class="text-center bg-white w-full p-4 rounded-lg">
                    <h1 class="text-3xl font-bold mb-2">Total Data Tamu</h1>
                    <h5 class="text-2xl">{{ $data['total_data_tamu'] }}</h5>
                </div>
                <div class="text-center bg-white w-full p-4 rounded-lg">
                    <h1 class="text-3xl font-bold mb-2">Total Kamar Terisi</h1>
                    <h5 class="text-2xl">{{ $data['total_kamar_terisi'] }}</h5>
                </div>
            </div>
            <div class="flex flex-col gap-2" style="height: 500px; color: black; border-radius: 0.75rem; gap: 1rem">
                <div class="text-center bg-white w-full p-4 rounded-lg">
                    <h1 class="text-3xl font-bold">
                        @livewire(\App\Filament\Admin\Widgets\StatusRoomData::class)
                    </h1>
                </div>
            </div>
            <div class="flex flex-col gap-2" style="height: 500px; color: black; border-radius: 0.75rem; gap: 1rem">
                <div class="text-center bg-white w-full p-4 rounded-lg">
                    <h1 class="text-3xl font-bold">
                        @livewire(\App\Filament\Admin\Widgets\BarMonthRoom::class)
                    </h1>
                </div>
            </div>
        </div>
        <div style="margin-top:2rem">
            @livewire(\App\Filament\Admin\Widgets\TableBooking::class)
        </div>
    </div>

</x-filament-panels::page>
