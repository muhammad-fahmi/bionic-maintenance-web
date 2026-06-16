<?php

namespace App\Http\Controllers;

use App\Contracts\WorkOrderServiceInterface;
use App\Http\Requests\AssignWorkOrderRequest;
use App\Http\Requests\CompleteWorkOrderRequest;
use App\Http\Requests\RateWorkOrderRequest;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class WorkOrderController
 *
 * Controller untuk menangani HTTP request yang berkaitan dengan entitas Work Order.
 */
class WorkOrderController extends Controller
{
    /**
     * @var WorkOrderServiceInterface
     */
    protected $workOrderService;

    /**
     * WorkOrderController constructor.
     *
     * @param  WorkOrderServiceInterface  $workOrderService  Layanan bisnis untuk Work Order.
     */
    public function __construct(WorkOrderServiceInterface $workOrderService)
    {
        $this->workOrderService = $workOrderService;
    }

    /**
     * Menampilkan daftar semua Work Order berdasarkan peran (role) pengguna.
     */
    public function index(): View
    {
        $workOrders = $this->workOrderService->getWorkOrdersForUser(Auth::user());

        return view('work-orders.index', compact('workOrders'));
    }

    /**
     * Menampilkan form pembuatan Work Order baru (khusus Pelapor).
     */
    public function create(): View
    {
        return view('reporter.work-orders.create');
    }

    /**
     * Menyimpan Work Order baru ke dalam sistem.
     *
     * @param  StoreWorkOrderRequest  $request  Validasi form request.
     */
    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        $workOrder = $this->workOrderService->createWorkOrder(
            $request->validated(),
            Auth::id()
        );

        return redirect()->route('reporter.dashboard')
            ->with('status', 'Work Order created successfully! Code: '.$workOrder->wo_code);
    }

    /**
     * Menampilkan detail spesifik dari sebuah Work Order.
     *
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['reporter', 'receiver', 'executor', 'activities']);

        return view('work-orders.show', compact('workOrder'));
    }

    /**
     * Menetapkan pelaksana (executor) pada Work Order yang sedang 'pending'.
     *
     * @param  AssignWorkOrderRequest  $request  Validasi penetapan request.
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function assign(AssignWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $this->workOrderService->assignWorkOrder(
            $workOrder,
            $request->validated('executor_id'),
            Auth::id()
        );

        return redirect()->route('receiver.dashboard')
            ->with('status', 'Work Order assigned successfully!');
    }

    /**
     * Memulai proses eksekusi Work Order oleh pelaksana.
     *
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function start(WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->executor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->workOrderService->startWorkOrder($workOrder);

        return redirect()->route('executor.work-orders.execute', $workOrder);
    }

    /**
     * Menampilkan halaman pengerjaan eksekusi Work Order.
     *
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function execute(WorkOrder $workOrder): View
    {
        if ($workOrder->executor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('executor.work-orders.execute', compact('workOrder'));
    }

    /**
     * Menyelesaikan Work Order dan merekam aktivitas pelaksana.
     *
     * @param  CompleteWorkOrderRequest  $request  Validasi penyelesaian request.
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function complete(CompleteWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->executor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->workOrderService->completeWorkOrder(
            $workOrder,
            $request->validated('activities')
        );

        return redirect()->route('executor.dashboard')
            ->with('status', 'Work Order completed successfully!');
    }

    /**
     * Memberikan rating kepuasan pada Work Order yang telah diselesaikan.
     *
     * @param  RateWorkOrderRequest  $request  Validasi rating request.
     * @param  WorkOrder  $workOrder  Model Work Order.
     */
    public function rate(RateWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->reporter_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($workOrder->status !== 'completed') {
            return back()->with('error', 'Hanya Work Order yang sudah selesai yang dapat dinilai.');
        }

        $this->workOrderService->rateWorkOrder(
            $workOrder,
            $request->validated('rating')
        );

        return back()->with('status', 'Terima kasih atas penilaian Anda!');
    }
}
