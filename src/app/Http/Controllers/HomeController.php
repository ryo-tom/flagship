<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $currentMonthRange = $this->getCurrentMonthRange();
        $inquiriesCountByStatus = $this->getInquiriesCountByStatus($currentMonthRange);
        $totalInquiriesCount = Inquiry::whereBetween('inquiry_date', $currentMonthRange)->count();

        return Inertia::render('Home', compact(
            'inquiriesCountByStatus',
            'totalInquiriesCount',
        ));
    }

    private function getInquiriesCountByStatus(array $dateTimeRange)
    {
        return Inquiry::whereBetween('inquiry_date', $dateTimeRange)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
    }

    private function getCurrentMonthRange(): array
    {
        $currentDate = Carbon::today();
        $firstDayOfCurrentMonth = $currentDate->copy()->startOfMonth()->startOfDay();
        $lastDayOfCurrentMonth  = $currentDate->copy()->endOfMonth()->endOfDay();
        
        return [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth];
    }
}
