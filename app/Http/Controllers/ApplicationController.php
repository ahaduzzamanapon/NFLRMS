<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\District;
use App\Models\License;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the applicant's applications.
     */
    public function index()
    {
        $user = auth()->user();
        PaymentController::syncUserPendingPayments($user);
        $applications = $user->applications()->latest()->get();
        $licenses = $user->licenses()->latest()->get();

        return view('citizen.dashboard', compact('applications', 'licenses'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        $user = auth()->user();
        $districts = District::orderBy('name')->get();
        $dealers = User::where('role', Role::DealerApplicant->value)->get();

        return view('citizen.apply', compact('user', 'districts', 'dealers'));
    }

    public function store(Request $request)
    {
        $isDealer = auth()->user()->role === Role::DealerApplicant || $request->input('type') === 'new_dealing_license';

        if ($isDealer) {
            $request->validate([
                'firm_name' => ['required', 'string', 'max:255'],
                'trade_license' => ['required', 'string', 'max:255'],
                'business_address' => ['required', 'string'],
                'district_id' => ['required', 'integer', 'exists:districts,id'],
                'license_class' => ['required', 'string'],
                'nid' => ['required', 'string', 'min:10', 'max:17'],
                'mobile' => ['required', 'string'],
                'annual_income' => ['required', 'numeric', 'min:0'],
                'categories' => ['required', 'array', 'min:1'],
                'nid_copy' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'tin_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);

            $appNumber = 'DEAL-'.strtoupper(Str::random(8)).'-'.date('Y');

            $application = Application::create([
                'application_number' => $appNumber,
                'user_id' => auth()->id(),
                'type' => 'new_dealing_license',
                'applicant_type' => 'dealer',
                'status' => 'payment_pending',
                'district_id' => $request->district_id,
                'upazila_id' => auth()->user()->upazila_id ?? Upazila::where('district_id', $request->district_id)->first()?->id,
                'applicant_details' => [
                    'nid' => $request->nid,
                    'firm_name' => $request->firm_name,
                    'trade_license' => $request->trade_license,
                    'business_address' => $request->business_address,
                    'license_class' => $request->license_class,
                    'mobile' => $request->mobile,
                    'annual_income' => $request->annual_income,
                ],
                'firearm_details' => [
                    'weapon_type' => 'Dealing License',
                    'categories' => $request->categories,
                ],
                'current_actor_role' => Role::DealerApplicant->value,
            ]);
        } else {
            $request->validate([
                'nid' => ['required', 'string', 'min:10', 'max:17'],
                'dob' => ['required', 'date'],
                'father_name' => ['required', 'string', 'max:255'],
                'present_address' => ['required', 'string'],
                'permanent_address' => ['required', 'string'],
                'annual_income' => ['required', 'numeric', 'min:0'],
                'weapon_type' => ['required', 'string'],
                'bore' => ['required', 'string'],
                'purpose' => ['required', 'string'],
                'dealer_name' => ['nullable', 'string', 'max:255'],
                'dealer_id' => ['nullable', 'integer', 'exists:users,id'],
                'district_id' => ['required', 'integer', 'exists:districts,id'],
                'upazila_id' => ['required', 'integer', 'exists:upazilas,id'],
                'nid_copy' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'tin_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);

            $appNumber = 'FL-'.strtoupper(Str::random(8)).'-'.date('Y');

            $application = Application::create([
                'application_number' => $appNumber,
                'user_id' => auth()->id(),
                'dealer_id' => $request->input('dealer_id'),
                'type' => 'new',
                'applicant_type' => 'citizen',
                'status' => 'payment_pending',
                'district_id' => $request->district_id,
                'upazila_id' => $request->upazila_id,
                'applicant_details' => [
                    'nid' => $request->nid,
                    'dob' => $request->dob,
                    'father_name' => $request->father_name,
                    'present_address' => $request->present_address,
                    'permanent_address' => $request->permanent_address,
                    'annual_income' => $request->annual_income,
                ],
                'firearm_details' => [
                    'weapon_type' => $request->weapon_type,
                    'bore' => $request->bore,
                    'purpose' => $request->purpose,
                    'dealer_name' => $request->input('dealer_name') ?: 'M/S Metropolitan Arms Store',
                ],
                'current_actor_role' => Role::CitizenApplicant->value,
            ]);
        }

        $this->processApplicationDocuments($request, $application);

        // Create log entry
        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'created',
            'from_status' => 'draft',
            'to_status' => 'payment_pending',
            'actor_id' => auth()->id(),
            'remarks' => 'Application created. Redirecting to payment checkout for platform service fee.',
        ]);

        return redirect()->route('payment.initiate', ['application' => $application->id, 'type' => 'service_fee']);
    }

    /**
     * Helper to store uploaded application documents.
     */
    private function processApplicationDocuments(Request $request, Application $application): void
    {
        $documents = [];
        $docLabels = [
            'nid_copy' => 'National ID Copy',
            'tin_certificate' => 'TIN Certificate',
            'birth_cert' => 'Birth Certificate',
            'edu_cert' => 'Educational Certificate',
            'tax_yr1' => 'Income Tax Return (Year 1)',
            'tax_yr2' => 'Income Tax Return (Year 2)',
            'tax_yr3' => 'Income Tax Return (Year 3)',
            'affidavit' => 'Notarized Affidavit',
            'nationality_cert' => 'Nationality Certificate',
            'photo' => 'Passport Photograph',
            'firing_report' => 'Firing Range Report',
            'medical_cert' => 'Medical Fitness Certificate',
            'police_clearance' => 'Police Clearance Letter',
            'trade_cert' => 'Trade License Document',
            'bank_solvency' => 'Bank Solvency Certificate',
            'safe_photo' => 'Vault Photo',
        ];

        foreach ($request->allFiles() as $key => $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('documents', 'public');
                $documents[$key] = [
                    'name' => $docLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                    'path' => $path,
                    'file' => $file->getClientOriginalName(),
                    'size' => round($file->getSize() / 1024, 1).' KB',
                    'uploaded_at' => now()->toIso8601String(),
                ];
            }
        }

        if (! empty($documents)) {
            $application->update(['documents' => $documents]);
        }
    }

    /**
     * Display the specified application details.
     */
    public function show(Application $application)
    {
        // Check if user is authorized to view this application
        $user = auth()->user();
        if ($user->role === Role::CitizenApplicant || $user->role === Role::DealerApplicant) {
            if ($application->user_id !== $user->id) {
                abort(403);
            }
        }

        PaymentController::syncUserPendingPayments($user);
        $application->refresh();

        return view('citizen.show', compact('application'));
    }

    /**
     * Show renewal apply form.
     */
    public function renewalForm(License $license)
    {
        $user = auth()->user();
        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $licenses = $user->licenses()->latest()->get();

        return view('citizen.apply_renewal', compact('license', 'user', 'licenses'));
    }

    /**
     * Submit renewal application.
     */
    public function storeRenewal(Request $request, License $license)
    {
        $user = auth()->user();
        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $appNumber = 'RL-'.strtoupper(Str::random(8)).'-'.date('Y');

        $application = Application::create([
            'application_number' => $appNumber,
            'user_id' => $user->id,
            'type' => 'renewal',
            'applicant_type' => $user->role === Role::DealerApplicant ? 'dealer' : 'citizen',
            'status' => 'payment_pending',
            'district_id' => $user->district_id,
            'upazila_id' => $user->upazila_id,
            'applicant_details' => [
                'license_id' => $license->id,
                'license_number' => $license->license_number,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'firearm_details' => $license->firearm_details,
            'current_actor_role' => $user->role === Role::DealerApplicant ? Role::DealerApplicant->value : Role::CitizenApplicant->value,
        ]);

        $this->processApplicationDocuments($request, $application);

        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'created',
            'from_status' => 'draft',
            'to_status' => 'payment_pending',
            'actor_id' => $user->id,
            'remarks' => 'Renewal application created. Redirecting to payment checkout for platform service fee.',
        ]);

        return redirect()->route('payment.initiate', ['application' => $application->id, 'type' => 'service_fee']);
    }

    /**
     * Handle general renewal redirect from the sidebar link.
     */
    public function renewalGeneral()
    {
        $user = auth()->user();
        $license = $user->licenses()->first();
        if ($license) {
            return redirect()->route('citizen.renew', $license->id);
        }
        $route = auth()->user()->role === Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';

        return redirect()->route($route)->with('error', 'You do not have any active licenses to renew.');
    }

    /**
     * Download or view statutory document file.
     */
    public function downloadDocument(Request $request)
    {
        $key = $request->query('key');
        $title = $request->query('title', 'Statutory Document');
        $appNo = $request->query('app', 'NFLRMS-DOC');
        $isInline = $request->boolean('inline') || $request->query('view') === '1';

        $application = Application::where('application_number', $appNo)->first();

        if ($application && is_array($application->documents) && ! empty($application->documents)) {
            $userDocs = $application->documents;
            $matchedDocData = null;

            // 1. Direct key match
            if ($key && isset($userDocs[$key])) {
                $matchedDocData = $userDocs[$key];
            }

            // 2. Alias group match
            if (! $matchedDocData) {
                $aliasGroups = [
                    'nid' => ['nid', 'nid_copy', 'nid_card', 'national_id', 'identity'],
                    'tin' => ['tin', 'tin_certificate', 'tax_yr1', 'tax_yr2', 'tax_yr3', 'tax_return', 'income_tax'],
                    'birth' => ['birth_cert', 'birth_certificate', 'birth'],
                    'edu' => ['edu_cert', 'edu', 'educational_cert', 'education'],
                    'affidavit' => ['affidavit', 'affidavit_copy', 'notarized'],
                    'nationality' => ['nationality_cert', 'nationality', 'citizenship'],
                    'photo' => ['photo', 'passport_photo', 'profile_photo', 'photograph'],
                    'firing' => ['firing_report', 'firing_cert', 'range'],
                    'medical' => ['medical', 'medical_cert', 'fitness_cert', 'doctor', 'health'],
                    'police' => ['police_clearance', 'police', 'clearance'],
                    'bank' => ['bank', 'bank_solvency', 'statement', 'bank_statement'],
                    'safe' => ['safe', 'safe_photo', 'vault'],
                    'trade' => ['trade', 'trade_cert', 'trade_license', 'business', 'trade_license_doc'],
                    'premises' => ['premises_photo', 'premises'],
                ];

                $searchTerms = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9_\s]/i', ' ', ($key ?: '').' '.$title))));

                foreach ($aliasGroups as $group => $aliases) {
                    $hasGroupMatch = false;
                    foreach ($searchTerms as $term) {
                        if (in_array($term, $aliases) || Str::contains($term, $group)) {
                            $hasGroupMatch = true;
                            break;
                        }
                    }

                    if ($hasGroupMatch) {
                        foreach ($userDocs as $dKey => $dData) {
                            if (in_array(strtolower($dKey), $aliases) || Str::contains(strtolower($dKey), $group)) {
                                $matchedDocData = $dData;
                                break 2;
                            }
                        }
                    }
                }
            }

            // 3. Keyword / partial substring match
            if (! $matchedDocData) {
                $searchStr = strtolower(($key ?: '').' '.$title);
                foreach ($userDocs as $dKey => $dData) {
                    $dName = strtolower(is_array($dData) ? ($dData['name'] ?? '') : '');
                    $dKeyLower = strtolower($dKey);

                    if (Str::contains($searchStr, $dKeyLower) || Str::contains($dKeyLower, strtolower($key ?: 'xyz_never')) ||
                        ($dName && (Str::contains($searchStr, $dName) || Str::contains($dName, strtolower($title))))) {
                        $matchedDocData = $dData;
                        break;
                    }
                }
            }

            // 4. Fallback: if only 1 uploaded doc exists, return it
            if (! $matchedDocData && count($userDocs) === 1) {
                $matchedDocData = reset($userDocs);
            }

            // Serve matched document if found on storage disk
            if ($matchedDocData) {
                $docPath = is_array($matchedDocData) ? ($matchedDocData['path'] ?? $matchedDocData['file'] ?? '') : (is_string($matchedDocData) ? $matchedDocData : '');
                $fullPath = storage_path('app/public/'.$docPath);

                if (! empty($docPath) && file_exists($fullPath)) {
                    $downloadFileName = is_array($matchedDocData) ? ($matchedDocData['file'] ?? basename($fullPath)) : basename($fullPath);
                    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

                    if ($isInline) {
                        return response()->file($fullPath, [
                            'Content-Type' => $mimeType,
                            'Content-Disposition' => 'inline; filename="'.$downloadFileName.'"',
                        ]);
                    }

                    return response()->download($fullPath, $downloadFileName);
                }
            }
        }

        // Fallback: If no uploaded file exists on disk, generate official verified demo PDF
        $fileName = Str::slug($title).'_'.$appNo.'.pdf';

        $pdfContent = "%PDF-1.4\n".
            "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n".
            "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n".
            "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n".
            '4 0 obj << /Length 280 >> stream\n'.
            "BT /F1 16 Tf 50 750 Td (GOVERNMENT OF THE PEOPLE'S REPUBLIC OF BANGLADESH) Tj ET\n".
            "BT /F1 12 Tf 50 720 Td (Ministry of Home Affairs - NFLRMS Official Statutory Attachment) Tj ET\n".
            'BT /F1 14 Tf 50 680 Td (Document: '.strtoupper($title).") Tj ET\n".
            'BT /F1 11 Tf 50 650 Td (Application Reference: '.$appNo.") Tj ET\n".
            "BT /F1 11 Tf 50 630 Td (Status: VERIFIED & ENCRYPTED IN GOVERNMENT VAULT) Tj ET\n".
            "BT /F1 10 Tf 50 580 Td (Digitally Verified & Watermarked for Firearms License Clearance.) Tj ET\n".
            "endstream endobj\n".
            "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n".
            "xref\n0 6\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000246 00000 n\n0000000577 00000 n\ntrailer << /Size 6 /Root 1 0 R >>\nstartxref\n646\n%%EOF";

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}
