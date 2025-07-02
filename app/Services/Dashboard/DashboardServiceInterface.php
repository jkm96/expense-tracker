<?php

namespace App\Services\Dashboard;

interface DashboardServiceInterface
{
    public function getDashboardData(int $userId): array;
}
