<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\License;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate payment for PayStation.
     */
    public function initiate(Request $request, Application $application)
    {
        $type = $request->query('type', 'service_fee');
        $user = auth()->user();

        // Calculate amount
        $amount = $this->calculateAmount($application, $type);

        if ($type === 'license_fee') {
            $application->update(['license_fee_amount' => $amount]);
        } else {
            $application->update(['service_fee_amount' => $amount]);
        }

        // Get paystation settings
        $storeId = Setting::get('pay_store_id');
        $storePass = Setting::get('pay_store_pass');
        $endpoint = Setting::get('pay_endpoint', 'https://api.paystation.com.bd/sandbox/initiate-payment');

        if (empty($storeId) || empty($storePass)) {
            return redirect()->route($user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard')
                ->with('error', 'PayStation Sandbox credentials are not configured. Please login as System Admin and set them in the API Configuration tab.');
        }

        // Real PayStation API Call
        try {
            $invoiceNumber = $application->application_number.'_'.($type === 'service_fee' ? 'SF' : 'LF');

            $response = Http::timeout(10)->post($endpoint, [
                'merchantId' => $storeId,
                'password' => $storePass,
                'invoice_number' => $invoiceNumber,
                'payment_amount' => $amount,
                'cust_name' => $user->name,
                'cust_phone' => $user->phone ?? '01711234567',
                'cust_email' => $user->email,
                'currency' => 'BDT',
                'callback_url' => route('payment.callback'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $checkoutUrl = $data['checkout_url'] ?? $data['payment_url'] ?? null;
                if ($checkoutUrl) {
                    return redirect($checkoutUrl);
                }
            }

            Log::error('PayStation initiation failed', ['response' => $response->body()]);

            return redirect()->route($user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard')
                ->with('error', 'PayStation payment initiation failed: '.($response->json()['message'] ?? 'Invalid response from PayStation.'));
        } catch (\Exception $e) {
            Log::error('PayStation initiation exception', ['message' => $e->getMessage()]);

            return redirect()->route($user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard')
                ->with('error', 'PayStation payment initiation error: '.$e->getMessage());
        }
    }

    /**
     * Verify callback from PayStation.
     */
    public function callback(Request $request)
    {
        $status = $request->input('status');
        $invoice = $request->input('invoice_number');
        $trxId = $request->input('trx_id');

        if (empty($invoice)) {
            return redirect()->route('login')->with('error', 'Invalid payment callback parameters.');
        }

        $isServiceFee = Str::endsWith($invoice, '_SF');
        $appNumber = Str::beforeLast($invoice, '_');

        $application = Application::where('application_number', $appNumber)->firstOrFail();
        $user = $application->user;

        // Force log user in if they aren't (since webhook/callback can be outside session)
        if (! Auth::check()) {
            Auth::login($user);
        }

        if ($status !== 'Successful') {
            return redirect()->route($user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard')
                ->with('error', 'Payment was '.strtolower($status).'. Please try again.');
        }

        // Save payment details
        $details = $application->payment_details ?? [];
        $keyPrefix = $isServiceFee ? 'service_fee_' : 'license_fee_';
        $details[$keyPrefix.'trx_id'] = $trxId;
        $details[$keyPrefix.'status'] = 'paid';
        $details[$keyPrefix.'date'] = now()->toDateTimeString();
        $details[$keyPrefix.'payload'] = $request->all();

        if ($isServiceFee) {
            $application->update([
                'service_fee_paid' => true,
                'status' => 'submitted',
                'current_actor_role' => Role::DcFrontDesk->value,
                'payment_details' => $details,
            ]);

            // Create log entry
            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'submitted',
                'from_status' => 'payment_pending',
                'to_status' => 'submitted',
                'actor_id' => $user->id,
                'remarks' => 'Platform service fee paid successfully. Trx ID: '.$trxId.'. Application routed to DC Front Desk.',
            ]);

            $route = $user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';

            return redirect()->route($route)->with('success', 'Application platform fee paid successfully! Tracking Code: '.$appNumber);
        } else {
            $application->update([
                'license_fee_paid' => true,
                'status' => 'approved',
                'payment_details' => $details,
            ]);

            // Issue the license
            $this->issueLicense($application);

            // Create log entry
            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'approved',
                'from_status' => 'waiting_for_license_fee',
                'to_status' => 'approved',
                'actor_id' => $user->id,
                'remarks' => 'License fee paid successfully. Trx ID: '.$trxId.'. Firearms license issued.',
            ]);

            $route = $user->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';

            return redirect()->route($route)->with('success', 'License fee paid successfully! Your license is now active.');
        }
    }

    /**
     * Calculate fee amount based on rules.
     */
    protected function calculateAmount(Application $application, $type)
    {
        if ($type === 'service_fee') {
            if ($application->type === 'renewal') {
                return Setting::get('fee_platform_renewal', 720);
            }

            return Setting::get('fee_platform_new', 850);
        }

        // License fee
        if ($application->applicant_type === 'dealer') {
            return 100000; // ৳100,000 flat license fee for dealing license
        }

        $weaponType = $application->firearm_details['weapon_type'] ?? '';
        $isHandgun = in_array($weaponType, ['Pistol', 'Revolver']);

        if ($application->type === 'renewal') {
            return $isHandgun
                ? Setting::get('fee_pistol_renewal', 20000)
                : Setting::get('fee_longgun_renewal', 10000);
        }

        return $isHandgun
            ? Setting::get('fee_pistol_new', 60000)
            : Setting::get('fee_longgun_new', 40000);
    }

    /**
     * System Helper: Issue the active license.
     */
    protected function issueLicense(Application $application)
    {
        $licenseNum = 'LIC-'.strtoupper(Str::random(10));

        License::create([
            'license_number' => $licenseNum,
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'type' => $application->applicant_type === 'dealer' ? 'dealer_dealing' : 'citizen_arms',
            'issue_date' => now(),
            'expiry_date' => now()->addYears(3),
            'status' => 'active',
            'firearm_details' => $application->firearm_details,
            'qrcode' => route('verify', ['license_number' => $licenseNum]),
        ]);
    }
}
