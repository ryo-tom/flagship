<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $currentMonthRange = $this->getCurrentMonthRange();
        $inquiriesCountByStatus = $this->getInquiriesCountByStatus($currentMonthRange);
        $inquiriesCountByType   = $this->getInquiriesCountByType($currentMonthRange);
        $totalInquiriesCount = Inquiry::whereBetween('inquiry_date', $currentMonthRange)->count();

        $salesOrdersCount = SalesOrder::searchByDeliveryPeriod(...$currentMonthRange)->count();

        return Inertia::render('Home', compact(
            'inquiriesCountByStatus',
            'inquiriesCountByType',
            'totalInquiriesCount',
            'salesOrdersCount',
        ));
    }

    private function getInquiriesCountByStatus(array $dateTimeRange)
    {
        return Inquiry::whereBetween('inquiry_date', $dateTimeRange)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
    }

    private function getInquiriesCountByType(array $dateTimeRange)
    {
        return Inquiry::whereBetween('inquiry_date', $dateTimeRange)
                ->with('inquiryType')
                ->selectRaw('inquiry_type_id, COUNT(*) as count')
                ->groupBy('inquiry_type_id')
                ->get()
                ->makeHidden('status_label');
    }

    private function getCurrentMonthRange(): array
    {
        $currentDate = Carbon::today();
        $firstDayOfCurrentMonth = $currentDate->copy()->startOfMonth()->startOfDay();
        $lastDayOfCurrentMonth  = $currentDate->copy()->endOfMonth()->endOfDay();

        return [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth];
    }
}
