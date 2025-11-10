<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            // Primary & Foreign Keys
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Informações da Empresa
            $table->string('company_name')->comment('Razão Social');
            $table->string('trade_name')->nullable()->comment('Nome Fantasia');
            $table->string('nif', 20)->unique()->comment('NIF - Número de Identificação Fiscal');
            $table->string('company_registration_number', 50)->nullable()->comment('Número de Registo Comercial');
            $table->enum('company_type', ['SA', 'Lda', 'Unipessoal', 'EI', 'Outro'])->nullable()->comment('Tipo de Empresa');
            $table->string('industry')->nullable()->comment('Setor/Indústria');
            
            // Endereço
            $table->text('company_address')->comment('Endereço Completo');
            $table->string('company_city', 100)->nullable();
            $table->string('company_state', 100)->nullable();
            $table->string('company_postal_code', 20)->nullable();
            $table->string('company_country', 100)->default('Angola');
            
            // Contactos
            $table->string('company_phone', 20)->comment('Telefone da Empresa');
            $table->string('company_email')->nullable();
            $table->string('website')->nullable();
            
            // Documentos (uploads)
            $table->string('logo')->nullable()->comment('Logo da empresa');
            $table->string('business_license_doc')->nullable()->comment('Alvará Comercial');
            $table->string('tax_certificate_doc')->nullable()->comment('Certidão Fiscal');
            
            // Informações Comerciais
            $table->enum('annual_revenue_range', ['0-500k', '500k-1M', '1M-5M', '5M+'])->nullable()->comment('Faturação Anual');
            $table->enum('employee_count_range', ['1-10', '11-50', '51-200', '200+'])->nullable()->comment('Número de Funcionários');
            $table->decimal('purchasing_volume_estimate', 15, 2)->nullable()->comment('Volume de Compras Mensal (AOA)');
            
            // Crédito e Pagamento
            $table->decimal('credit_limit_requested', 15, 2)->nullable()->comment('Limite de Crédito Solicitado');
            $table->decimal('credit_limit_approved', 15, 2)->nullable()->comment('Limite de Crédito Aprovado');
            $table->enum('payment_terms_preference', ['0', '30', '60', '90'])->default('0')->comment('Prazo de Pagamento (dias)');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable()->comment('IBAN');
            
            // Observações
            $table->text('delivery_notes')->nullable()->comment('Observações para Entrega');
            
            // Status e Aprovação
            $table->enum('status', ['incomplete', 'pending', 'approved', 'rejected'])->default('incomplete');
            $table->text('admin_notes')->nullable()->comment('Notas do Administrador');
            $table->timestamp('verified_at')->nullable()->comment('Data de Aprovação');
            $table->unsignedInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->comment('Admin que Aprovou');
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('nif');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
