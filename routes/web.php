<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    // Shared Dashboard redirector
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isReceiver()) {
            return redirect()->route('receiver.dashboard');
        } elseif ($user->isExecutor()) {
            return redirect()->route('executor.dashboard');
        }

        return redirect()->route('reporter.dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('work-orders', WorkOrderController::class)->only(['index', 'show']);
    });

    // Reporter Routes
    Route::middleware(['role:reporter'])->prefix('reporter')->name('reporter.')->group(function () {
        Route::get('/dashboard', function () {
            $workOrders = WorkOrder::where('reporter_id', auth()->id())->latest()->paginate(10);

            return view('reporter.dashboard', compact('workOrders'));
        })->name('dashboard');

        Route::resource('work-orders', WorkOrderController::class)->only(['create', 'store', 'index', 'show']);
        Route::post('/work-orders/{workOrder}/rate', [WorkOrderController::class, 'rate'])->name('work-orders.rate');
    });

    // Receiver (Dispatcher) Routes
    Route::middleware(['role:receiver'])->prefix('receiver')->name('receiver.')->group(function () {
        Route::get('/dashboard', function () {
            $pendingWorkOrders = WorkOrder::where('status', 'pending')->latest()->paginate(10);
            $executors = User::where('role', 'executor')->get();

            return view('receiver.dashboard', compact('pendingWorkOrders', 'executors'));
        })->name('dashboard');

        Route::resource('work-orders', WorkOrderController::class)->only(['index', 'show']);
        Route::post('/work-orders/{workOrder}/assign', [WorkOrderController::class, 'assign'])->name('work-orders.assign');
    });

    // Executor Routes
    Route::middleware(['role:executor'])->prefix('executor')->name('executor.')->group(function () {
        Route::get('/dashboard', function () {
            $assignedWorkOrders = WorkOrder::where('executor_id', auth()->id())
                ->whereIn('status', ['assigned', 'in_progress'])
                ->latest()
                ->paginate(10);

            return view('executor.dashboard', compact('assignedWorkOrders'));
        })->name('dashboard');

        Route::resource('work-orders', WorkOrderController::class)->only(['index', 'show']);
        Route::post('/work-orders/{workOrder}/start', [WorkOrderController::class, 'start'])->name('work-orders.start');
        Route::get('/work-orders/{workOrder}/execute', [WorkOrderController::class, 'execute'])->name('work-orders.execute');
        Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
