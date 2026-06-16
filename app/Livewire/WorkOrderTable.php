<?php

namespace App\Livewire;

use App\Models\WorkOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class WorkOrderTable extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';

    public $status = '';

    public $priority = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPriority()
    {
        $this->resetPage();
    }

    public function generateDummyData()
    {
        $reporter = User::where('role', 'reporter')->first() ?? User::first();
        
        if ($reporter) {
            $dummies = [];
            for ($i = 1; $i <= 35; $i++) {
                $dummies[] = [
                    'title' => '[DUMMY] Laporan Kerusakan ' . $i . ' - ' . now()->timestamp,
                    'description' => 'Laporan kerusakan otomatis untuk test paginasi.',
                    'status' => 'pending',
                    'priority' => 'medium',
                    'reporter_id' => $reporter->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            WorkOrder::insert($dummies);
            session()->flash('message', '35 Laporan Dummy berhasil ditambahkan!');
        }
    }

    public function deleteDummyData()
    {
        WorkOrder::where('title', 'like', '[DUMMY]%')->delete();
        session()->flash('message', 'Semua Laporan Dummy berhasil dihapus!');
    }

    public function render()
    {
        $query = WorkOrder::with('reporter');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('wo_code', 'like', '%'.$this->search.'%')
                    ->orWhere('title', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        return view('livewire.work-order-table', [
            'workOrders' => $query->latest()->paginate(10),
        ]);
    }
}
