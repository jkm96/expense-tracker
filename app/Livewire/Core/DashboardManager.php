<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Services\Dashboard\DashboardServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardManager extends Component
{
    public function render(DashboardServiceInterface $dashboardService)
    {
        $userId = Auth::id();
        $data = $dashboardService->getDashboardData($userId);

        return view('livewire.core.dashboard-manager', $data);
    }
}
