<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfessionalFieldsToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            // Contato
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('company_email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('company_email');

            // Dados Fiscais
            $table->string('tax_id', 50)->nullable()->unique()->after('website');
            $table->string('registration_number', 50)->nullable()->after('tax_id');

            // Classificação
            $table->string('company_type', 50)->nullable()->after('registration_number');
            $table->string('industry', 100)->nullable()->after('company_type');

            // Endereço Detalhado
            $table->string('city', 100)->nullable()->after('industry');
            $table->string('province', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('province');

            // Documentos (KYC)
            $table->string('business_license_path')->nullable()->after('postal_code');
            $table->string('tax_certificate_path')->nullable()->after('business_license_path');

            // Dados Bancários (para pagamentos)
            $table->string('bank_name')->nullable()->after('tax_certificate_path');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('iban')->nullable()->after('bank_account_name');

            // Notas Admin
            $table->text('admin_notes')->nullable()->after('iban');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'company_email',
                'website',
                'tax_id',
                'registration_number',
                'company_type',
                'industry',
                'city',
                'province',
                'postal_code',
                'business_license_path',
                'tax_certificate_path',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'iban',
                'admin_notes',
            ]);
        });
    }
}
