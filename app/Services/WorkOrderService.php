<?php

namespace App\Services;

use App\Contracts\WorkOrderServiceInterface;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class WorkOrderService
 *
 * Implementasi layanan untuk menangani logika bisnis terkait Work Order.
 */
class WorkOrderService implements WorkOrderServiceInterface
{
    /**
     * Dapatkan daftar work order dengan paginasi, disesuaikan berdasarkan peran pengguna.
     *
     * @param  User  $user
     */
    public function getWorkOrdersForUser($user): LengthAwarePaginator
    {
        if ($user->isReporter()) {
            return WorkOrder::with('reporter')->where('reporter_id', $user->id)->latest()->paginate(10);
        } elseif ($user->isReceiver()) {
            return WorkOrder::with('reporter')->latest()->paginate(10);
        } elseif ($user->isExecutor()) {
            return WorkOrder::with('reporter')->where('executor_id', $user->id)->latest()->paginate(10);
        }

        return WorkOrder::with('reporter')->latest()->paginate(20);
    }

    /**
     * Buat Work Order baru.
     *
     * @param  array  $data  Data Work Order (title, description, priority).
     * @param  int  $reporterId  ID dari pelapor.
     */
    public function createWorkOrder(array $data, int $reporterId): WorkOrder
    {
        return WorkOrder::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'reporter_id' => $reporterId,
            'status' => 'pending',
        ]);
    }

    /**
     * Tugaskan Work Order ke seorang pelaksana (executor).
     *
     * @param  WorkOrder  $workOrder  Objek Work Order.
     * @param  int  $executorId  ID dari pelaksana.
     * @param  int  $receiverId  ID dari penerima (dispatcher) yang menetapkan.
     */
    public function assignWorkOrder(WorkOrder $workOrder, int $executorId, int $receiverId): bool
    {
        return $workOrder->update([
            'executor_id' => $executorId,
            'receiver_id' => $receiverId,
            'status' => 'assigned',
        ]);
    }

    /**
     * Mulai proses pengerjaan Work Order.
     */
    public function startWorkOrder(WorkOrder $workOrder): bool
    {
        return $workOrder->update(['status' => 'in_progress']);
    }

    /**
     * Selesaikan Work Order dan catat semua aktivitas pengerjaannya.
     *
     * @param  array  $activities  Daftar deskripsi aktivitas.
     *
     * @throws \Exception
     */
    public function completeWorkOrder(WorkOrder $workOrder, array $activities): bool
    {
        DB::beginTransaction();
        try {
            foreach ($activities as $description) {
                $workOrder->activities()->create([
                    'description' => $description,
                ]);
            }

            $workOrder->update(['status' => 'completed']);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Berikan penilaian (rating) kepuasan untuk Work Order yang telah selesai.
     *
     * @param  int  $rating  Nilai rating (1-10).
     */
    public function rateWorkOrder(WorkOrder $workOrder, int $rating): bool
    {
        return $workOrder->update([
            'satisfaction_rating' => $rating,
        ]);
    }
}
