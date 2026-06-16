<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboardStats extends Component
{
    public function render()
    {
        $userCount = User::count();
        $woCount = WorkOrder::count();

        $satisfactionData = WorkOrder::whereNotNull('satisfaction_rating')
            ->select('satisfaction_rating', DB::raw('count(*) as total'))
            ->groupBy('satisfaction_rating')
            ->orderBy('satisfaction_rating')
            ->get();

        $totalRatings = $satisfactionData->sum('total');

        $satisfactionStats = $satisfactionData->map(function ($item) use ($totalRatings) {
            return [
                'rating' => $item->satisfaction_rating,
                'count' => $item->total,
                'percentage' => $totalRatings > 0 ? round(($item->total / $totalRatings) * 100, 1) : 0,
            ];
        });

        return view('livewire.admin-dashboard-stats', [
            'userCount' => $userCount,
            'woCount' => $woCount,
            'satisfactionStats' => $satisfactionStats,
            'totalRatings' => $totalRatings,
        ]);
    }
}
