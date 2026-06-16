<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface WorkOrderServiceInterface
 *
 * Abstraksi untuk layanan logika bisnis terkait Work Order.
 */
interface WorkOrderServiceInterface
{
    /**
     * Dapatkan daftar work order dengan paginasi, disesuaikan berdasarkan peran pengguna.
     *
     * @param  User  $user
     */
    public function getWorkOrdersForUser($user): LengthAwarePaginator;

    /**
     * Buat Work Order baru.
     */
    public function createWorkOrder(array $data, int $reporterId): WorkOrder;

    /**
     * Tugaskan Work Order ke seorang pelaksana (executor).
     */
    public function assignWorkOrder(WorkOrder $workOrder, int $executorId, int $receiverId): bool;

    /**
     * Mulai proses pengerjaan Work Order.
     */
    public function startWorkOrder(WorkOrder $workOrder): bool;

    /**
     * Selesaikan Work Order dan tambahkan aktivitas.
     */
    public function completeWorkOrder(WorkOrder $workOrder, array $activities): bool;

    /**
     * Berikan penilaian (rating) kepuasan untuk Work Order yang telah selesai.
     */
    public function rateWorkOrder(WorkOrder $workOrder, int $rating): bool;
}
