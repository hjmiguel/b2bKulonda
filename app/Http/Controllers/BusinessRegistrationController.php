<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\B2B\RegistrationReceived;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessRegistrationController extends Controller
{
    /**
     * Exibe o formulário de registro B2B
     */
    public function showForm()
    {
        return view('frontend.business_register');
    }

    /**
     * Processa o registro de cliente B2B
     */
    public function register(Request $request)
    {
        // Validação
        $validated = $request->validate([
            // Dados Pessoais
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',

            // Dados Empresa
            'company_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'tax_id' => 'required|string|max:50|unique:business_profiles',
            'registration_number' => 'nullable|string|max:50',
            'company_type' => 'nullable|string|max:50',
            'industry' => 'nullable|string|max:100',

            // Endereço
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'province' => 'nullable|string|max:100',

            // Contato Empresa
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',

            // Detalhes Negócio
            'annual_revenue_range' => 'nullable|string|max:50',
            'employee_count' => 'nullable|integer|min:0',
            'estimated_monthly_purchases' => 'nullable|numeric|min:0',
            'credit_limit_requested' => 'nullable|numeric|min:0',
            'payment_terms_preference' => 'nullable|integer|in:0,30,60,90',

            // Documentos
            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_address' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Outros
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 1. Criar usuário
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'user_type' => 'customer',
                'approval_status' => 'pending', // Aguardando aprovação
            ]);

            // 2. Upload documentos
            $businessLicensePath = $request->file('business_license')->store('business_documents', 'public');
            $taxCertificatePath = $request->file('tax_certificate')->store('business_documents', 'public');
            $proofAddressPath = $request->hasFile('proof_address')
                ? $request->file('proof_address')->store('business_documents', 'public')
                : null;

            // 3. Criar perfil business
            $profile = BusinessProfile::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'trade_name' => $validated['trade_name'] ?? null,
                'tax_id' => $validated['tax_id'],
                'registration_number' => $validated['registration_number'] ?? null,
                'company_type' => $validated['company_type'] ?? null,
                'industry' => $validated['industry'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'] ?? null,
                'province' => $validated['province'] ?? null,
                'company_phone' => $validated['company_phone'] ?? null,
                'company_email' => $validated['company_email'] ?? null,
                'website' => $validated['website'] ?? null,
                'annual_revenue_range' => $validated['annual_revenue_range'] ?? null,
                'employee_count' => $validated['employee_count'] ?? null,
                'estimated_monthly_purchases' => $validated['estimated_monthly_purchases'] ?? null,
                'credit_limit_requested' => $validated['credit_limit_requested'] ?? null,
                'payment_terms_preference' => $validated['payment_terms_preference'] ?? 30,
                'business_license_path' => $businessLicensePath,
                'tax_certificate_path' => $taxCertificatePath,
                'proof_address_path' => $proofAddressPath,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            // Enviar email de confirmação de registro            Mail::to($user->email)->send(new RegistrationReceived($user, $profile));
            // 4. Login automático
            Auth::login($user);

            // 5. Flash message
            flash(translate('Your B2B registration has been submitted successfully. Our team will review your application within 24-48 hours.'))->success();

            // 6. Redirect para página de aguardando aprovação
            return redirect()->route('approval.pending');

        } catch (\Exception $e) {
            DB::rollBack();

            // Deletar arquivos se houver erro
            if (isset($businessLicensePath)) Storage::disk('public')->delete($businessLicensePath);
            if (isset($taxCertificatePath)) Storage::disk('public')->delete($taxCertificatePath);
            if (isset($proofAddressPath)) Storage::disk('public')->delete($proofAddressPath);

            flash(translate('An error occurred during registration. Please try again.'))->error();
            return back()->withInput();
        }
    }
}
