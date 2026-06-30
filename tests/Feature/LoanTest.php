<?php

namespace Tests\Feature;

use App\Commodity;
use App\Loan;
use App\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    private function createUser(string $name, string $email, string $role): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('secret'),
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function createCommodity(string $name, int $quantity = 10): Commodity
    {
        return Commodity::create([
            'item_code' => 'BRG-' . mt_rand(1000, 9999),
            'name' => $name,
            'brand' => 'Brand',
            'material' => 'Material',
            'year_of_purchase' => date('Y'),
            'condition' => 1,
            'quantity' => $quantity,
            'price' => $quantity * 1000,
            'price_per_item' => 1000,
            'note' => 'Note',
        ]);
    }

    public function test_admin_and_kahim_can_see_all_loans()
    {
        $admin = $this->createUser('Admin', 'admin_test@mail.com', 'Administrator');
        $kahim = $this->createUser('Kahim', 'kahim_test@mail.com', 'Ketua Himpunan');
        $staff1 = $this->createUser('Staff 1', 'staff1_test@mail.com', 'Staff Himpunan');
        $staff2 = $this->createUser('Staff 2', 'staff2_test@mail.com', 'Staff Himpunan');

        $commodity1 = $this->createCommodity('Laptop A');
        $commodity2 = $this->createCommodity('Laptop B');

        $loan1 = Loan::create([
            'user_id' => $staff1->id,
            'commodity_id' => $commodity1->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        $loan2 = Loan::create([
            'user_id' => $staff2->id,
            'commodity_id' => $commodity2->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        // Admin should see both loans
        $response = $this->actingAs($admin)->get(route('peminjaman.index'));
        $response->assertStatus(200);
        $response->assertViewHas('loans', function ($loans) use ($loan1, $loan2) {
            return $loans->contains($loan1) && $loans->contains($loan2);
        });

        // Ketua Himpunan should see both loans
        $response = $this->actingAs($kahim)->get(route('peminjaman.index'));
        $response->assertStatus(200);
        $response->assertViewHas('loans', function ($loans) use ($loan1, $loan2) {
            return $loans->contains($loan1) && $loans->contains($loan2);
        });
    }

    public function test_other_roles_only_see_own_loans()
    {
        $staff1 = $this->createUser('Staff 1', 'staff1_test@mail.com', 'Staff Himpunan');
        $staff2 = $this->createUser('Staff 2', 'staff2_test@mail.com', 'Staff Himpunan');

        $commodity1 = $this->createCommodity('Laptop A');
        $commodity2 = $this->createCommodity('Laptop B');

        $loan1 = Loan::create([
            'user_id' => $staff1->id,
            'commodity_id' => $commodity1->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        $loan2 = Loan::create([
            'user_id' => $staff2->id,
            'commodity_id' => $commodity2->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        // Staff 1 should only see loan 1
        $response = $this->actingAs($staff1)->get(route('peminjaman.index'));
        $response->assertStatus(200);
        $response->assertViewHas('loans', function ($loans) use ($loan1, $loan2) {
            return $loans->contains($loan1) && !$loans->contains($loan2);
        });
    }

    public function test_cannot_borrow_already_borrowed_commodity()
    {
        $user1 = $this->createUser('Staff 1', 'staff1_test@mail.com', 'Staff Himpunan');
        $user2 = $this->createUser('Staff 2', 'staff2_test@mail.com', 'Staff Himpunan');

        $commodity = $this->createCommodity('Laptop A');

        // User 1 borrows the commodity
        Loan::create([
            'user_id' => $user1->id,
            'commodity_id' => $commodity->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        // User 2 tries to borrow the same commodity
        $response = $this->actingAs($user2)->post(route('peminjaman.store'), [
            'commodity_id' => $commodity->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHas('error', 'Barang ini sedang dipinjam oleh orang lain dan belum dikembalikan!');
    }

    public function test_user_cannot_return_other_users_loan()
    {
        $user1 = $this->createUser('Staff 1', 'staff1_test@mail.com', 'Staff Himpunan');
        $user2 = $this->createUser('Staff 2', 'staff2_test@mail.com', 'Staff Himpunan');

        $commodity = $this->createCommodity('Laptop A');

        // User 1 borrows the commodity
        $loan = Loan::create([
            'user_id' => $user1->id,
            'commodity_id' => $commodity->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        // Create a dummy file for return upload
        $file = \Illuminate\Http\UploadedFile::fake()->image('proof.jpg');

        // User 2 tries to return User 1's loan
        $response = $this->actingAs($user2)->put(route('peminjaman.update', $loan->id), [
            'return_photo' => $file,
            'return_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_return_own_loan()
    {
        $user1 = $this->createUser('Staff 1', 'staff1_test@mail.com', 'Staff Himpunan');

        $commodity = $this->createCommodity('Laptop A');

        // User 1 borrows the commodity
        $loan = Loan::create([
            'user_id' => $user1->id,
            'commodity_id' => $commodity->id,
            'quantity' => 1,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        // Create a dummy file for return upload
        $file = \Illuminate\Http\UploadedFile::fake()->image('proof.jpg');

        // User 1 returns their own loan
        $response2 = $this->actingAs($user1)->put(route('peminjaman.update', $loan->id), [
            'return_photo' => $file,
            'return_date' => now()->format('Y-m-d'),
        ]);

        $response2->assertSessionHas('success', 'Barang berhasil dikembalikan!');
        $this->assertEquals('dikembalikan', $loan->fresh()->status);
    }
}
