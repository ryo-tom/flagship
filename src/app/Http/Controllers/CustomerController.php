<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSearchRequest;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\PurchaseTerm;
use App\Models\SalesTerm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customer/Create', [
            'userSelectOptions' => User::all(),
        ]);
    }

    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        $customer = DB::transaction(function () use ($request) {
            $createdCustomer = $this->createCustomer($request);
            $this->createPurchaseTermIfNeeded($request, $createdCustomer->id);
            $this->createSalesTermIfNeeded($request, $createdCustomer->id);
            return $createdCustomer;
        });

        return to_route('customers.index')
                ->with('message', "取引先ID:{$customer->id} 登録成功しました。");
    }

    public function show(Customer $customer): Response
    {
        $customer->load(['contacts.inChargeUser', 'inChargeUser', 'createdBy', 'updatedBy', 'logisticsAddresses']);

        return Inertia::render('Customer/Show', [
            'customer' => array_merge($customer->toArray(), [
                'created_at' => $customer->created_at->format('Y-m-d H:i'),
                'updated_at' => $customer->updated_at->format('Y-m-d H:i'),
            ]),
            'userSelectOptions' => User::all(),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customer/Edit', [
            'customer' => $customer,
            'userSelectOptions' => User::all(),
        ]);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update([
            'name'              => $request->input('name'),
            'name_kana'         => $request->input('name_kana'),
            'shortcut'          => $request->input('shortcut'),
            'postal_code'       => $request->input('postal_code'),
            'address'           => $request->input('address'),
            'tel'               => $request->input('tel'),
            'fax'               => $request->input('fax'),
            'note'              => $request->input('note'),
            'in_charge_user_id' => $request->input('in_charge_user_id'),
            'updated_by_id'     => auth()->user()->id,
        ]);

        return to_route('customers.index')
            ->with('message', "取引先ID:{$customer->id} 更新しました。");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->contacts()->exists()) {
            return redirect()
                ->route('customers.edit', $customer)
                ->with('message', 'この取引先は連絡先データを持つため削除できません。');
        }

        $customer->delete();
        return to_route('customers.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */
    private function createCustomer(CustomerStoreRequest $request): Customer
    {
        $createdCustomer = Customer::create([
            'name'              => $request->input('name'),
            'name_kana'         => $request->input('name_kana'),
            'shortcut'          => $request->input('shortcut'),
            'postal_code'       => $request->input('postal_code'),
            'address'           => $request->input('address'),
            'tel'               => $request->input('tel'),
            'fax'               => $request->input('fax'),
            'note'              => $request->input('note'),
            'in_charge_user_id' => $request->input('in_charge_user_id'),
            'created_by_id'     => auth()->user()->id,
        ]);

        return $createdCustomer;
    }

    private function createPurchaseTermIfNeeded(CustomerStoreRequest $request, int $customerId): void
    {
        if ($request->input('purchase_billing_type') !== null) {
            PurchaseTerm::create([
                'customer_id'           => $customerId,
                'billing_type'          => $request->input('purchase_billing_type'),
                'cutoff_day'            => $request->input('purchase_cutoff_day'),
                'payment_month_offset'  => $request->input('purchase_payment_month_offset'),
                'payment_day'           => $request->input('purchase_payment_day'),
                'payment_day_offset'    => $request->input('purchase_payment_day_offset'),
            ]);
        }
    }

    private function createSalesTermIfNeeded(CustomerStoreRequest $request, int $customerId): void
    {
        if ($request->input('sales_billing_type') !== null) {
            SalesTerm::create([
                'customer_id'           => $customerId,
                'billing_type'          => $request->input('sales_billing_type'),
                'cutoff_day'            => $request->input('sales_cutoff_day'),
                'payment_month_offset'  => $request->input('sales_payment_month_offset'),
                'payment_day'           => $request->input('sales_payment_day'),
                'payment_day_offset'    => $request->input('sales_payment_day_offset'),
            ]);
        }
    }
}
