<?php

namespace App\Console\Commands;

use App\Models\WorkOrder;
use Illuminate\Console\Command;

class ManageDummyWorkOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:work-orders {--remove : Hapus semua data dummy} {--count=50 : Jumlah data dummy yang dibuat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate atau hapus laporan (work order) dummy untuk mengetes paginasi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $remove = $this->option('remove');

        if ($remove) {
            $this->info('Menghapus semua laporan dummy...');
            $count = WorkOrder::where('title', 'LIKE', '[DUMMY]%')->delete();
            $this->info("Berhasil menghapus {$count} laporan dummy.");

            return;
        }

        $count = (int) $this->option('count');
        $this->info("Membuat {$count} laporan dummy...");

        WorkOrder::factory()->count($count)->create();

        $this->info("Berhasil membuat {$count} laporan dummy.");
    }
}
