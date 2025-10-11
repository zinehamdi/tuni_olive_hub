<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->enum('incoterm', ['fob','cif']);
            $table->string('port_from');
            $table->string('port_to');
            $table->string('vessel')->nullable();
            $table->timestamp('etd_at')->nullable();
            $table->timestamp('eta_at')->nullable();
            $table->enum('status', ['draft','docs_pending','ready','shipped','arrived','cleared','closed'])->default('draft')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('export_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('export_shipments')->cascadeOnDelete();
            $table->enum('type', ['commercial_invoice','packing_list','coa','origin','phytosanitary','insurance','bl','other']);
            $table->string('url');
            $table->timestamp('verified_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('currency', ['TND','USD','EUR']);
            $table->decimal('subtotal', 14, 3);
            $table->decimal('tax', 14, 3)->default(0);
            $table->decimal('total', 14, 3);
            $table->enum('status', ['draft','issued','paid','void'])->default('issued')->index();
            $table->string('pdf_url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 14, 3);
            $table->enum('currency', ['TND','USD','EUR']);
            $table->enum('status', ['pending','processing','paid','failed'])->default('pending')->index();
            $table->enum('provider', ['bank','transfer','manual']);
            $table->string('provider_ref')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('export_documents');
        Schema::dropIfExists('export_shipments');
    }
};
