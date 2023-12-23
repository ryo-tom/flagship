<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSearchRequest;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\DeliveryAddress;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(CustomerSearchRequest $request): Response
    {
        $customers = Customer::query()
            ->with(['inChargeUser'])
            ->searchByKeyword($request->input('keyword'))
            ->searchById($request->input('customer_id'))
            ->searchByAddress($request->input('address'))
            ->searchByPhone($request->input('phone'))
            ->searchByInCharge($request->input('in_charge_user_id'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('Customer/Index', [
            'customers'             => $customers,
            'inChargeUserOptions'   => User::hasCustomers()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customer/Create', [
            'userOptions'       => User::active()->get(),
            'leadSourceOptions' => LeadSource::all(),
        ]);
    }

    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        $customer = DB::transaction(function () use ($request) {
            $createdCustomer = $this->createCustomer($request);
            $this->appendPurchaseTerm($createdCustomer, $request->input('purchase_term'));
            $this->appendSalesTerm($createdCustomer, $request->input('sales_term'));
            $this->createContacts($createdCustomer->id, $request->input('contacts'));
            $this->createDeliveryAddresses($request, $createdCustomer);
            return $createdCustomer;
        });

        return to_route('customers.show', $customer)
            ->with('message', "取引先ID:{$customer->id} 登録成功しました。");
    }

    public function show(Customer $customer): Response
    {
        $customer->load([
            'contacts.inChargeUser',
            'inChargeUser',
            'createdBy',
            'updatedBy',
            'deliveryAddresses',
            'purchaseTerm',
            'salesTerm',
            'contacts.salesActivities.inChargeUser',
            'contacts.leadSource',
        ]);

        return Inertia::render('Customer/Show', [
            'customer'          => $customer,
            'userOptions'       => User::active()->get(),
            'leadSourceOptions' => LeadSource::all(),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        $customer->load([
            'purchaseTerm',
            'salesTerm',
            'contacts',
            'deliveryAddresses',
            'createdBy',
            'updatedBy'
        ]);

        return Inertia::render('Customer/Edit', [
            'customer'          => $customer,
            'userOptions'       => User::active()->get(),
            'leadSourceOptions' => LeadSource::all(),
        ]);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): RedirectResponse
    {
        DB::transaction(function () use ($request, &$customer) {
            $this->updateCustomer($request, $customer);
            $this->updateOrCreatePurchaseTerm($customer, $request->input('purchase_term'));
            $this->updateOrCreateSalesTerm($customer, $request->input('sales_term'));
            $this->upsertContacts($request, $customer);
            $this->upsertDeliveryAddresses($request, $customer);
        });

        return to_route('customers.show', $customer)
            ->with('message', "取引先情報を更新しました。");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if (!$customer->canDelete()) {
            return redirect()
                ->route('customers.edit', $customer)
                ->with('message', 'この取引先は関連データを持つため削除できません。');
        }

        $customer->delete();
        return to_route('customers.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
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

    private function appendPurchaseTerm(Customer $customer, ?array $purchaseTerms): void
    {
        if (!$purchaseTerms) {
            return;
        }

        $customer->purchaseTerm()->create($purchaseTerms);
    }

    private function appendSalesTerm(Customer $customer, ?array $salesTerms): void
    {
        if (!$salesTerms) {
            return;
        }

        $customer->salesTerm()->create($salesTerms);
    }

    private function updateCustomer(CustomerUpdateRequest $request, Customer $customer): void
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
    }

    private function updateOrCreatePurchaseTerm(Customer $customer, ?array $purchaseTerms): void
    {
        if (!$purchaseTerms) {
            return;
        }

        $customer->purchaseTerm()->updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'billing_type'          => $purchaseTerms['billing_type'] ?? null,
                'cutoff_day'            => $purchaseTerms['cutoff_day'] ?? null,
                'payment_month_offset'  => $purchaseTerms['payment_month_offset'] ?? null,
                'payment_day'           => $purchaseTerms['payment_day'] ?? null,
                'payment_day_offset'    => $purchaseTerms['payment_day_offset'] ?? null,
            ],
        );
    }

    private function updateOrCreateSalesTerm(Customer $customer, ?array $salesTerms): void
    {
        if (!$salesTerms) {
            return;
        }

        $customer->salesTerm()->updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'billing_type'          => $salesTerms['billing_type'] ?? null,
                'cutoff_day'            => $salesTerms['cutoff_day'] ?? null,
                'payment_month_offset'  => $salesTerms['payment_month_offset'] ?? null,
                'payment_day'           => $salesTerms['payment_day'] ?? null,
                'payment_day_offset'    => $salesTerms['payment_day_offset'] ?? null,
            ],
        );
    }

    private function createContacts(string $customerId, ?array $contacts): void
    {
        if (!$contacts) {
            return;
        }

        $contactsData = collect($contacts)->map(function ($contact) use ($customerId) {
            return  [
                'customer_id'       => $customerId,
                'lead_source_id'    => $contact['lead_source_id'] ?? null,
                'name'              => $contact['name'] ?? null,
                'name_kana'         => $contact['name_kana'] ?? null,
                'tel'               => $contact['tel'] ?? null,
                'mobile_number'     => $contact['mobile_number'] ?? null,
                'email'             => $contact['email'] ?? null,
                'position'          => $contact['position'] ?? null,
                'role'              => $contact['role'] ?? null,
                'is_active'         => $contact['is_active'] ?? null,
                'note'              => $contact['note'] ?? null,
                'in_charge_user_id' => $contact['in_charge_user_id'] ?? null,
                'created_by_id'     => auth()->user()->id,
                'created_at'        => now(),
            ];
        })->toArray();

        CustomerContact::insert($contactsData);
    }


    private function upsertContacts(CustomerUpdateRequest $request, Customer $customer): void
    {
        $contacts = $this->prepareUpdateContactsData($request->input('contacts'), $customer);
        CustomerContact::upsert($contacts, ['id']);
    }

    /**
     * リクエストから受け取ったcontactsデータを、
     * customer_contactsテーブル用に既存データ更新と新規追加のデータとして整形する。
     *
     * @param array $contacts リクエストから受け取った連絡先データ
     * @param Customer $customer 連絡先に関連付ける取引先インスタンス
     * @return array 整形された連絡先データ
     */
    private function prepareUpdateContactsData(array $contacts, Customer $customer): array
    {
        $updatedById = auth()->user()->id;
        $customerId  = $customer->id;

        return collect($contacts)
            ->map(function ($contact) use ($customerId, $updatedById) {
                return [
                    'id'                => $contact['id'] ?? null,
                    'customer_id'       => $customerId,
                    'name'              => $contact['name'],
                    'name_kana'         => $contact['name_kana'],
                    'tel'               => $contact['tel'],
                    'mobile_number'     => $contact['mobile_number'],
                    'email'             => $contact['email'],
                    'position'          => $contact['position'],
                    'role'              => $contact['role'],
                    'is_active'         => $contact['is_active'],
                    'note'              => $contact['note'],
                    'in_charge_user_id' => $contact['in_charge_user_id'],
                    'lead_source_id'    => $contact['lead_source_id'],
                    'created_by_id'     => $contact['created_by_id'] ?? $updatedById,
                    'updated_by_id'     => $updatedById,
                ];
            })->toArray();
    }

    private function createDeliveryAddresses(CustomerStoreRequest $request, Customer $customer): void
    {
        $deliveryAddresses = $this->prepareDeliveryAddressesData($request->input('delivery_addresses'), $customer);
        DeliveryAddress::insert($deliveryAddresses);
    }

    /**
     * リクエストから受け取ったdelivery_addressesデータを、
     * delivery_addressesテーブル用のデータとして整形する。
     *
     * @param array $delivery_addresses リクエストから受け取った連絡先データ
     * @param Customer $customer 関連付ける取引先インスタンス
     * @return array 整形された配送情報データ
     */
    private function prepareDeliveryAddressesData(array $delivery_addresses, Customer $customer): array
    {
        $customerId  = $customer->id;

        return collect($delivery_addresses)->map(function ($contact) use ($customerId) {
            return array_merge($contact, [
                'customer_id'   => $customerId,
            ]);
        })->toArray();
    }

    private function upsertDeliveryAddresses(CustomerUpdateRequest $request, Customer $customer): void
    {
        $deliveryAddresses = $this->prepareUpdateDeliveryAddressesData($request->input('delivery_addresses'), $customer);
        DeliveryAddress::upsert($deliveryAddresses, ['id']);
    }

    private function prepareUpdateDeliveryAddressesData(array $deliveryAddresses, Customer $customer): array
    {
        $customerId  = $customer->id;

        return collect($deliveryAddresses)
            ->map(function ($deliveryAddress) use ($customerId) {
                return [
                    'id'                => $deliveryAddress['id'] ?? null,
                    'customer_id'       => $customerId,
                    'address_type'      => $deliveryAddress['address_type'],
                    'postal_code'       => $deliveryAddress['postal_code'],
                    'address'           => $deliveryAddress['address'],
                    'company_name'      => $deliveryAddress['company_name'],
                    'contact_name'      => $deliveryAddress['contact_name'],
                    'tel'               => $deliveryAddress['tel'],
                    'note'              => $deliveryAddress['note'],
                ];
            })->toArray();
    }
}
