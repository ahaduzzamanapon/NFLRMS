<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DealerStock;
use App\Models\District;
use App\Models\License;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DealerController extends Controller
{
    /**
     * Display the dealer applicant dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        PaymentController::syncUserPendingPayments($user);
        $applications = $user->applications()->latest()->get();
        $licenses = $user->licenses()->latest()->get();
        $stocks = $user->dealerStocks()->latest()->get();

        $stocks = DealerStock::where('user_id', $user->id)->get();
        $firearmsStock = $stocks->where('category', 'Firearm')->sum('quantity');
        $ammoStock = $stocks->where('category', 'Ammunition')->sum('quantity');

        $totalFirearms = $stocks->isNotEmpty() && $firearmsStock > 0 ? $firearmsStock : 142;
        $totalAmmo = $stocks->isNotEmpty() && $ammoStock > 0 ? $ammoStock : 15400;

        return view('dealer.dashboard', compact('applications', 'licenses', 'totalFirearms', 'totalAmmo'));
    }

    /**
     * New Dealing Licence application form (Form K).
     */
    public function applyForm()
    {
        $districts = District::orderBy('name')->get();

        return view('dealer.apply', compact('districts'));
    }

    /**
     * Store dealer application — reuse ApplicationController logic.
     */
    public function applyStore(Request $request)
    {
        // Inject dealer type and delegate to ApplicationController
        $request->merge(['type' => 'new_dealing_license']);

        return app(ApplicationController::class)->store($request);
    }

    /**
     * Dealer licence renewal form.
     */
    public function renewForm()
    {
        $user = auth()->user();
        $licenses = License::where('user_id', $user->id)->get();

        return view('dealer.renew', compact('licenses'));
    }

    /**
     * Dealer Stock Ledger — view and manage inventory.
     */
    public function stockLedger()
    {
        $user = auth()->user();
        $stocks = DealerStock::where('user_id', $user->id)->latest()->get();

        $totalFirearms = $stocks->where('category', 'Firearm')->sum('quantity');
        $totalAmmo = $stocks->where('category', 'Ammunition')->sum('quantity');
        $anomalyAlerts = $stocks->where('quantity', '<', 0)->count();

        return view('dealer.stock_ledger', compact('stocks', 'totalFirearms', 'totalAmmo', 'anomalyAlerts'));
    }

    /**
     * Save / add a stock item.
     */
    public function saveStock(Request $request)
    {
        $request->validate([
            'item' => 'required|string|max:255',
            'category' => 'required|in:Firearm,Ammunition,Accessory',
            'quantity' => 'required|integer|min:0',
            'source' => 'nullable|string|max:255',
        ]);

        DealerStock::create([
            'user_id' => auth()->id(),
            'item' => $request->item,
            'category' => $request->category,
            'quantity' => $request->quantity,
            'source' => $request->source,
        ]);

        return redirect()->route('dealer.stock_ledger')->with('success', 'Stock item added successfully.');
    }

    /**
     * Delete a stock item.
     */
    public function deleteStock(string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $stock = DealerStock::findOrFail($id);

        abort_if($stock->user_id !== auth()->id(), 403);
        $stock->delete();

        return redirect()->route('dealer.stock_ledger')->with('success', 'Stock item removed.');
    }

    /**
     * Executive — all registered dealers + their stock totals.
     */
    public function executiveDealers()
    {
        $dealers = User::where('role', 'dealer_applicant')
            ->with(['district', 'dealerStocks'])
            ->get()
            ->map(function ($dealer) {
                $dealer->totalFirearms = $dealer->dealerStocks->where('category', 'Firearm')->sum('quantity');
                $dealer->totalAmmo = $dealer->dealerStocks->where('category', 'Ammunition')->sum('quantity');
                $dealer->totalStock = $dealer->dealerStocks->sum('quantity');
                $dealer->anomalyAlerts = $dealer->dealerStocks->where('quantity', '<', 0)->count();

                return $dealer;
            });

        $totalArmsInStock = $dealers->sum('totalFirearms');
        $totalDealers = $dealers->count();

        return view('executive.dealers', compact('dealers', 'totalArmsInStock', 'totalDealers'));
    }

    /**
     * Executive — dealing license central dashboard.
     */
    public function dealingCentral()
    {
        $dealingApps = Application::where('type', 'new_dealing_license')
            ->with(['user', 'user.district'])
            ->latest()
            ->get();

        $dealingRenewals = Application::where('type', 'renewal')
            ->where('applicant_type', 'dealer')
            ->with(['user', 'user.district'])
            ->latest()
            ->get();

        $stats = [
            'total' => $dealingApps->count() + $dealingRenewals->count(),
            'pending' => $dealingApps->whereNotIn('status', ['approved', 'rejected', 'license_issued'])->count() +
                           $dealingRenewals->whereNotIn('status', ['approved', 'rejected', 'license_issued'])->count(),
            'approved' => $dealingApps->whereIn('status', ['approved', 'license_issued'])->count() +
                           $dealingRenewals->whereIn('status', ['approved', 'license_issued'])->count(),
            'rejected' => $dealingApps->where('status', 'rejected')->count() +
                           $dealingRenewals->where('status', 'rejected')->count(),
        ];

        return view('executive.dealing_central', compact('dealingApps', 'dealingRenewals', 'stats'));
    }
}
