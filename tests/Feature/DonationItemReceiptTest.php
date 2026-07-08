<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DonationItemReceiptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role')->default('admin');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('donation_item_receipts')) {
            Schema::create('donation_item_receipts', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_number')->unique();
                $table->date('received_date');
                $table->string('donor_name')->nullable();
                $table->string('donor_phone')->nullable();
                $table->string('item_type');
                $table->string('item_name')->nullable();
                $table->string('quantity')->nullable();
                $table->string('unit')->nullable();
                $table->string('item_condition')->nullable();
                $table->string('delivery_method')->nullable();
                $table->text('note')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->string('received_by')->nullable();
                $table->string('proof_photo')->nullable();
                $table->string('status')->default('received');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('donation_item_commitments')) {
            Schema::create('donation_item_commitments', function (Blueprint $table) {
                $table->id();
                $table->string('reference_number')->unique();
                $table->dateTime('received_at');
                $table->string('donor_name')->nullable();
                $table->string('donor_phone')->nullable();
                $table->string('item_type');
                $table->string('item_name')->nullable();
                $table->string('quantity_estimate')->nullable();
                $table->string('delivery_method')->nullable();
                $table->text('note')->nullable();
                $table->text('raw_whatsapp_message')->nullable();
                $table->string('status')->default('pending');
                $table->dateTime('confirmed_at')->nullable();
                $table->foreignId('received_receipt_id')->nullable();
                $table->foreignId('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('school_settings')) {
            Schema::create('school_settings', function (Blueprint $table) {
                $table->id();
                $table->string('school_name')->nullable();
                $table->string('tagline')->nullable();
                $table->string('logo_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_manual_item_receipt_create_redirects_to_whatsapp_data(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.infaq-barang.create'))
            ->assertRedirect(route('admin.infaq-barang-wa.index'))
            ->assertSessionHas('success', 'Penerimaan Infaq Barang harus dibuat dari Data WA Infaq Barang.');

        $this->actingAs($admin)->post(route('admin.infaq-barang.store'), [
            'received_date' => '2026-07-09',
            'donor_name' => 'Bapak Ahmad',
            'donor_phone' => '081234567890',
            'item_type' => 'Beras',
            'item_name' => 'Beras premium',
            'quantity' => '25',
            'unit' => 'kg',
            'item_condition' => 'Baik',
            'delivery_method' => 'Diantar ke sekolah',
            'received_by' => 'Admin Test',
        ])
            ->assertRedirect(route('admin.infaq-barang-wa.index'))
            ->assertSessionHas('success', 'Penerimaan Infaq Barang harus dibuat dari Data WA Infaq Barang.');

        $this->assertDatabaseCount('donation_item_receipts', 0);
    }
}
