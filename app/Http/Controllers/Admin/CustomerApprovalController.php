<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\B2B\ApplicationApproved;
use App\Mail\B2B\ApplicationRejected;
use App\Mail\B2B\RequestMoreInfo;
use App\Mail\B2B\CreditLimitUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerApprovalController extends Controller
{
    /**
     * Lista de clientes B2B pendentes
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = BusinessProfile::with('user')
            ->whereHas('user', function($q) {
                $q->where('user_type', 'customer');
            });

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $profiles = $query->latest()->paginate(20);

        return view('backend.b2b_customers.index', compact('profiles', 'status'));
    }

    /**
     * Exibe detalhes de um perfil B2B para aprovação
     */
    public function show($id)
    {
        $profile = BusinessProfile::with('user')->findOrFail($id);

        return view('backend.b2b_customers.show', compact('profile'));
    }

    /**
     * Aprova um cliente B2B
     */
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms' => 'required|integer|in:0,30,60,90',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $profile = BusinessProfile::with('user')->findOrFail($id);
            $user = $profile->user;

            // Atualizar perfil
            $profile->update([
                'status' => 'approved',
                'credit_limit' => $validated['credit_limit'],
                'credit_available' => $validated['credit_limit'], // Crédito inicial = limite
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'] ?? $profile->notes,
            ]);

            // Atualizar user
            $user->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            Mail::to($user->email)->send(new ApplicationApproved($user, $profile, $validated['credit_limit'], $validated['payment_terms']));
            // Mail::to($user->email)->send(new ApplicationApproved($user, $profile, $validated['credit_limit'], $validated['payment_terms']));

            DB::commit();

            flash(translate('B2B customer approved successfully!'))->success();
            return redirect()->route('admin.b2b_customers.index', ['status' => 'approved']);

        } catch (\Exception $e) {
            DB::rollBack();
            flash(translate('An error occurred. Please try again.'))->error();
            return back();
        }
    }

    /**
     * Rejeita um cliente B2B
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $profile = BusinessProfile::with('user')->findOrFail($id);
            $user = $profile->user;

            // Atualizar perfil
            $profile->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Atualizar user
            $user->update([
                'approval_status' => 'rejected',
            ]);

            Mail::to($user->email)->send(new ApplicationRejected($user, $profile, $validated['rejection_reason']));
            // Mail::to($user->email)->send(new ApplicationRejected($user, $profile, $validated['rejection_reason']));

            DB::commit();

            flash(translate('B2B customer rejected.'))->warning();
            return redirect()->route('admin.b2b_customers.index', ['status' => 'rejected']);

        } catch (\Exception $e) {
            DB::rollBack();
            flash(translate('An error occurred. Please try again.'))->error();
            return back();
        }
    }

    /**
     * Solicitar mais informações
     */
    public function requestInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $profile = BusinessProfile::with('user')->findOrFail($id);

            // Atualizar notas
            $profile->update([
                'notes' => ($profile->notes ? $profile->notes . "\n\n" : '') .
                          '[' . now()->format('Y-m-d H:i') . '] Info requested: ' . $validated['message'],
            ]);

            Mail::to($profile->user->email)->send(new RequestMoreInfo($profile->user, $profile, $validated['message']));
            // Mail::to($profile->user->email)->send(new RequestMoreInfo($profile->user, $profile, $validated['message']));

            flash(translate('Request sent successfully!'))->success();
            return back();

        } catch (\Exception $e) {
            flash(translate('An error occurred. Please try again.'))->error();
            return back();
        }
    }

    /**
     * Atualizar limite de crédito
     */
    public function updateCredit(Request $request, $id)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'payment_terms' => 'required|integer|in:0,30,60,90',
        ]);

        try {
            $profile = BusinessProfile::with('user')->findOrFail($id);
            $user = $profile->user;

            $oldCreditLimit = $profile->credit_limit;
            $newCreditLimit = $validated['credit_limit'];

            // Ajustar crédito disponível proporcionalmente
            $creditUsed = $oldCreditLimit - $profile->credit_available;
            $newCreditAvailable = max(0, $newCreditLimit - $creditUsed);

            $profile->update([
                'credit_limit' => $newCreditLimit,
                'credit_available' => $newCreditAvailable,
                'payment_terms' => $validated['payment_terms'],
            ]);

            Mail::to($user->email)->send(new CreditLimitUpdated($user, $profile, $oldCreditLimit, $newCreditLimit));
            // Mail::to($user->email)->send(new CreditLimitUpdated($user, $profile, $oldCreditLimit, $newCreditLimit));

            flash(translate('Credit limit updated successfully!'))->success();
            return back();

        } catch (\Exception $e) {
            flash(translate('An error occurred. Please try again.'))->error();
            return back();
        }
    }

    /**
     * Suspender cliente
     */
    public function suspend(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            $profile = BusinessProfile::with('user')->findOrFail($id);

            $profile->update([
                'status' => 'suspended',
                'notes' => ($profile->notes ? $profile->notes . "\n\n" : '') .
                          '[' . now()->format('Y-m-d H:i') . '] Suspended: ' . $validated['reason'],
            ]);

            $profile->user->update([
                'approval_status' => 'suspended',
            ]);

            flash(translate('Customer suspended.'))->warning();
            return back();

        } catch (\Exception $e) {
            flash(translate('An error occurred. Please try again.'))->error();
            return back();
        }
    }
}
