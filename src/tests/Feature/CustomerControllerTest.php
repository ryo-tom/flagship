<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_認証済みユーザーは取引先一覧を閲覧できる(): void
    {
        $this->actingAs($this->user);

        Customer::factory()->count(5)->create();

        $customersCount = Customer::count();

        $response = $this->get(route('customers.index'));
        $response->assertStatus(200);

        $response->assertInertia(function (Assert $page) use ($customersCount) {
            $page->component('Customer/Index');

            $page->has('customersPaginator', function (Assert $paginator) use ($customersCount) {

                $paginator->hasAll([
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]);

                $paginator->has('data', $customersCount, function (Assert $customer) {
                    $customer->hasAll([
                        'id',
                        'name',
                        'name_kana',
                        'shortcut',
                        'postal_code',
                        'address',
                        'tel_number',
                        'fax_number',
                        'note',
                        'in_charge_user_id',
                        'created_by_id',
                        'updated_by_id',
                        'created_at',
                        'updated_at',
                        'in_charge_user',
                        'contacts',
                    ]);
                });
            });
        });
    }
}
