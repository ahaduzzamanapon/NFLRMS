@extends('layouts.app')
@section('title', 'API Configuration')

@section('content')
<form method="POST" action="{{ route('admin.api_config.save') }}" class="max-w-3xl space-y-5">
    @csrf

    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">API Configuration</h2>
            <p class="text-xs text-slate-500 mt-1">Third-party integrations · secrets stored encrypted · rotate keys quarterly (BRS §5.14 · NFR-SEC-04)</p>
        </div>
        <button type="submit" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
            <span>💾</span><span>Save All Settings</span>
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex space-x-1 border-b border-slate-200" id="api-tabs">
        @foreach(['SMS Gateway','Email (SMTP)','Payment Gateway','NID / Identity','Webhooks'] as $i => $tab)
        <button type="button" onclick="switchTab({{ $i }})" id="tab-{{ $i }}"
                class="px-4 py-2.5 text-xs font-bold transition-colors border-b-2 -mb-px
                {{ $i === 0 ? 'border-gov-green text-gov-green' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            {{ $tab }}
        </button>
        @endforeach
    </div>

    <!-- Tab Panels -->
    @php
    $panels = [
        [
            'title' => 'SMS Gateway',
            'desc'  => 'Bulk transactional SMS · used for OTP, renewal reminders, status change alerts.',
            'providers' => ['SSL Wireless','Grameenphone SMSC','Robi Aggregator','Banglalink Enterprise'],
            'fields' => [
                ['label'=>'API Endpoint','name'=>'sms_endpoint','type'=>'text','placeholder'=>'https://smsplus.sslwireless.com/api/v3/send-sms','value'=>\App\Models\Setting::get('sms_endpoint', 'https://smsplus.sslwireless.com/api/v3/send-sms')],
                ['label'=>'API Token','name'=>'sms_token','type'=>'password','placeholder'=>'••••••••••••••••','value'=>\App\Models\Setting::get('sms_token', '')],
                ['label'=>'SID / Sender ID','name'=>'sms_sid','type'=>'text','placeholder'=>'MoHA-NFLRMS','value'=>\App\Models\Setting::get('sms_sid', 'MoHA-NFLRMS')],
                ['label'=>'Character Encoding','name'=>'sms_encoding','type'=>'text','placeholder'=>'UTF-8 (Bangla + English)','value'=>\App\Models\Setting::get('sms_encoding', 'UTF-8 (Bangla + English)')],
                ['label'=>'Rate Limit per Second','name'=>'sms_rate','type'=>'number','placeholder'=>'50','value'=>\App\Models\Setting::get('sms_rate', '50')],
            ],
        ],
        [
            'title' => 'Email (SMTP)',
            'desc'  => 'Transactional email for notifications and certificate delivery.',
            'providers' => ['Gmail SMTP','AWS SES','SendGrid','Mailgun'],
            'fields' => [
                ['label'=>'SMTP Host','name'=>'smtp_host','type'=>'text','placeholder'=>'smtp.gmail.com','value'=>\App\Models\Setting::get('smtp_host', 'smtp.gmail.com')],
                ['label'=>'SMTP Port','name'=>'smtp_port','type'=>'number','placeholder'=>'587','value'=>\App\Models\Setting::get('smtp_port', '587')],
                ['label'=>'SMTP Username','name'=>'smtp_user','type'=>'text','placeholder'=>'noreply@moha.gov.bd','value'=>\App\Models\Setting::get('smtp_user', 'noreply@moha.gov.bd')],
                ['label'=>'SMTP Password','name'=>'smtp_pass','type'=>'password','placeholder'=>'••••••••••','value'=>\App\Models\Setting::get('smtp_pass', '')],
                ['label'=>'From Name','name'=>'smtp_from','type'=>'text','placeholder'=>'NFLRMS · MoHA','value'=>\App\Models\Setting::get('smtp_from', 'NFLRMS · MoHA')],
            ],
        ],
        [
            'title' => 'Payment Gateway',
            'desc'  => 'Online fee payment integration via PayStation BD.',
            'providers' => ['PayStation Sandbox','bKash','Nagad'],
            'fields' => [
                ['label'=>'Gateway Endpoint','name'=>'pay_endpoint','type'=>'text','placeholder'=>'https://api.paystation.com.bd/initiate-payment','value'=>\App\Models\Setting::get('pay_endpoint', env('PAYSTATION_BASE_URL', 'https://api.paystation.com.bd').'/initiate-payment')],
                ['label'=>'Merchant ID (Store ID)','name'=>'pay_store_id','type'=>'text','placeholder'=>'2233-1771313076','value'=>\App\Models\Setting::get('pay_store_id', env('PAYSTATION_MERCHANT_ID', '2233-1771313076'))],
                ['label'=>'API Password (Store Password)','name'=>'pay_store_pass','type'=>'password','placeholder'=>'••••••••••','value'=>\App\Models\Setting::get('pay_store_pass', env('PAYSTATION_PASSWORD', ''))],
            ],
        ],
        [
            'title' => 'NID / Identity',
            'desc'  => 'Bangladesh NID server integration for applicant verification.',
            'providers' => ['EC NID Server','Porichoy API'],
            'fields' => [
                ['label'=>'NID API Endpoint','name'=>'nid_endpoint','type'=>'text','placeholder'=>'https://nidw.gov.bd/nid-pub/v3/verify','value'=>\App\Models\Setting::get('nid_endpoint', 'https://nidw.gov.bd/nid-pub/v3/verify')],
                ['label'=>'Client ID','name'=>'nid_client_id','type'=>'text','placeholder'=>'MOHA-NFLRMS-001','value'=>\App\Models\Setting::get('nid_client_id', 'MOHA-NFLRMS-001')],
                ['label'=>'Client Secret','name'=>'nid_secret','type'=>'password','placeholder'=>'••••••••••••••••','value'=>\App\Models\Setting::get('nid_secret', '')],
            ],
        ],
        [
            'title' => 'Webhooks',
            'desc'  => 'Outbound webhooks for status change events.',
            'providers' => [],
            'fields' => [
                ['label'=>'Application Approved URL','name'=>'wh_approved','type'=>'text','placeholder'=>'https://your-system.gov.bd/hooks/approved','value'=>\App\Models\Setting::get('wh_approved', '')],
                ['label'=>'License Issued URL','name'=>'wh_issued','type'=>'text','placeholder'=>'https://your-system.gov.bd/hooks/issued','value'=>\App\Models\Setting::get('wh_issued', '')],
                ['label'=>'Secret Key (HMAC)','name'=>'wh_secret','type'=>'password','placeholder'=>'••••••••••••••••','value'=>\App\Models\Setting::get('wh_secret', '')],
            ],
        ],
    ];
    @endphp

    @foreach($panels as $i => $panel)
    <div id="panel-{{ $i }}" class="{{ $i !== 0 ? 'hidden' : '' }}">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $panel['title'] }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $panel['desc'] }}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" class="px-3 py-1.5 border border-slate-200 text-xs font-bold text-slate-600 rounded-lg hover:bg-slate-50">Test connection</button>
                    <button type="submit" class="px-3 py-1.5 bg-gov-green hover:bg-gov-light text-white text-xs font-bold rounded-lg shadow-sm">Save</button>
                </div>
            </div>

            @if(!empty($panel['providers']))
            <div class="p-5 border-b border-slate-100">
                <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-2">Provider</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($panel['providers'] as $j => $provider)
                    <button type="button" class="py-2 px-3 rounded-lg border text-xs font-bold transition-colors
                        {{ $j === 0 ? 'border-gov-green bg-gov-green/5 text-gov-green' : 'border-slate-200 text-slate-600 hover:border-gov-green hover:text-gov-green' }}">
                        {{ $provider }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($panel['fields'] as $field)
                <div class="{{ $field['name'] === 'sms_endpoint' || $field['name'] === 'pay_endpoint' || $field['name'] === 'nid_endpoint' ? 'sm:col-span-2' : '' }}">
                    <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">{{ $field['label'] }}</label>
                    <div class="relative">
                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" placeholder="{{ $field['placeholder'] }}" value="{{ $field['value'] }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        @if($field['type'] === 'password')
                        <button type="button" onclick="togglePasswordVisibility(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-[10px]">👁</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</form>
@endsection

@section('scripts')
<script>
function switchTab(idx) {
    document.querySelectorAll('[id^="panel-"]').forEach((p,i) => p.classList.toggle('hidden', i !== idx));
    document.querySelectorAll('[id^="tab-"]').forEach((t,i) => {
        t.className = t.className.replace(/border-gov-green text-gov-green|border-transparent text-slate-500 hover:text-slate-800/g, '');
        t.className += i === idx ? ' border-gov-green text-gov-green' : ' border-transparent text-slate-500 hover:text-slate-800';
    });
}

function togglePasswordVisibility(btn) {
    const input = btn.previousElementSibling;
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }
}
</script>
@endsection
