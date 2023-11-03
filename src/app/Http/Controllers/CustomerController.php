<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSearchRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(CustomerSearchRequest $request): Response
    {
        $keyword    = $request->input('keyword', '');

        $customerQuery = Customer::query()
                        ->with(['inChargeUser', 'contacts'])
                        ->searchByKeyword($keyword);
        $customersPaginator = $customerQuery->paginate(20)->withQueryString();

        return Inertia::render('Customer/Index', [
            'customersPaginator' => $customersPaginator,
            'canAdmin'           => Gate::allows('admin'),
        ]);
    }
}
