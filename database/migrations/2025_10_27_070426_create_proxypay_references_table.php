<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tabela proxypay_references
 *
 * Reutilizável para qualquer projeto Laravel
 *
 * INSTRUÇÕES:
 * 1. Copiar para database/migrations/
 * 2. Renomear para: YYYY_MM_DD_HHMMSS_create_proxypay_references_table.php
 * 3. Executar: php artisan migrate
 */
class CreateProxypayReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxypay_references', function (Blueprint $table) {
            $table->id();

            // Dados da referência ProxyPay
            $table->string('reference_id')->unique()->comment('ID único da referência (9 dígitos)');
            $table->string('entity')->comment('Entidade ProxyPay (ex: 30061)');
            $table->string('reference')->comment('Número da referência EMIS');

            // Valores
            $table->decimal('amount', 12, 2)->comment('Valor em Kwanzas (AOA)');
            $table->dateTime('end_datetime')->comment('Data/hora de expiração');

            // Status
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled'])
                  ->default('pending')
                  ->index()
                  ->comment('Status do pagamento');

            // Relacionamento com pedido/transação (adaptar ao seu projeto)
            $table->string('order_id')->nullable()->index()->comment('ID do pedido/transação');

            // Campos personalizados
            $table->json('custom_fields')->nullable()->comment('Dados adicionais (JSON)');

            // Dados do pagamento
            $table->string('payment_id')->nullable()->comment('ID do pagamento ProxyPay');
            $table->dateTime('paid_at')->nullable()->comment('Data/hora do pagamento');

            // Timestamps padrão Laravel
            $table->timestamps();

            // Índices para performance
            $table->index('status');
            $table->index('end_datetime');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxypay_references');
    }
}
