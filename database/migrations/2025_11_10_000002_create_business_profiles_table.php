<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            
            // Company Information
            $table->string('company_name');
            $table->string('trade_name')->nullable();
            $table->string('tax_id')->unique(); // NIF
            $table->string('registration_number')->nullable();
            $table->string('company_type')->nullable(); // Lda, SA, etc
            $table->string('industry')->nullable();
            
            // Address
            $table->text('address');
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->string('province')->nullable();
            
            // Contact
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('website')->nullable();
            
            // Business Details
            $table->string('annual_revenue_range')->nullable();
            $table->integer('employee_count')->nullable();
            $table->decimal('estimated_monthly_purchases', 15, 2)->nullable();
            $table->decimal('credit_limit_requested', 15, 2)->nullable();
            $table->integer('payment_terms_preference')->default(30); // dias
            
            // Documents
            $table->string('business_license_path')->nullable(); // Certidão Comercial
            $table->string('tax_certificate_path')->nullable(); // NIF Document
            $table->string('proof_address_path')->nullable();
            
            // Credit Management
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('credit_available', 15, 2)->default(0);
            $table->integer('payment_terms')->default(30); // dias
            
            // Status
            $table->string('status', 20)->default('pending'); // pending/approved/rejected/suspended
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_profiles');
    }
}
