<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OverviewController extends Controller
{
    /**
     * Get the consistent dummy datasets for Firearms and Ammunition.
     */
    public static function getFirearmsData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Brig. Gen. (Retd.) Tariq Mahmud',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-8841',
                'item_type' => 'Pistol (Glock 17)',
                'bore' => '9mm',
                'quantity' => 2,
                'status' => 'Active',
                'nid_trade' => '1965882910482',
                'district' => 'Dhaka',
                'thana' => 'Gulshan',
                'address' => 'House 42, Road 11, Block D, Gulshan-1, Dhaka',
                'issue_date' => '2022-03-15',
                'expiry_date' => '2027-03-14',
                'issuer' => 'Ministry of Home Affairs (MoHA)',
                'verified_date' => '2026-08-20',
            ],
            [
                'id' => 2,
                'name' => 'Dhaka Arms & Co.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0104',
                'item_type' => 'Shotgun (Remington 870)',
                'bore' => '12 Gauge',
                'quantity' => 35,
                'status' => 'In Stock',
                'nid_trade' => 'TR-DH-2021-9940',
                'district' => 'Dhaka',
                'thana' => 'Motijheel',
                'address' => '14 Toyenbee Circular Road, Motijheel, Dhaka',
                'issue_date' => '2020-01-10',
                'expiry_date' => '2028-01-09',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-15',
            ],
            [
                'id' => 3,
                'name' => 'Md. Rafiqul Islam',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-1042',
                'item_type' => 'Hunting Rifle (Winchester 70)',
                'bore' => '.308 Win',
                'quantity' => 1,
                'status' => 'Active',
                'nid_trade' => '1991234567890',
                'district' => 'Dhaka',
                'thana' => 'Dhanmondi',
                'address' => 'Flat 5B, House 12, Road 5A, Dhanmondi, Dhaka',
                'issue_date' => '2023-06-20',
                'expiry_date' => '2028-06-19',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-10',
            ],
            [
                'id' => 4,
                'name' => 'Messrs Armoury House Ltd.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0218',
                'item_type' => 'Revolver (Smith & Wesson)',
                'bore' => '.32 ACP',
                'quantity' => 40,
                'status' => 'In Stock',
                'nid_trade' => 'TR-CTG-2019-4412',
                'district' => 'Chattogram',
                'thana' => 'Kotwali',
                'address' => '88 Station Road, Kotwali, Chattogram',
                'issue_date' => '2019-11-05',
                'expiry_date' => '2027-11-04',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-22',
            ],
            [
                'id' => 5,
                'name' => 'Fahim Ahmed Chowdhury',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-3310',
                'item_type' => 'Double Barrel Shotgun',
                'bore' => '12 Gauge',
                'quantity' => 1,
                'status' => 'Active',
                'nid_trade' => '1975678901234',
                'district' => 'Sylhet',
                'thana' => 'Sadarganj',
                'address' => 'Chowdhury Bari, Zinda Bazar, Sylhet',
                'issue_date' => '2021-09-12',
                'expiry_date' => '2026-09-11',
                'issuer' => 'District Commissioner Office, Sylhet',
                'verified_date' => '2026-08-18',
            ],
            [
                'id' => 6,
                'name' => 'Bengal Sports & Arms',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0355',
                'item_type' => 'Pistol (CZ 75 B)',
                'bore' => '9mm',
                'quantity' => 25,
                'status' => 'In Stock',
                'nid_trade' => 'TR-RAJ-2022-1108',
                'district' => 'Rajshahi',
                'thana' => 'Boalia',
                'address' => '22 Saheb Bazar Road, Boalia, Rajshahi',
                'issue_date' => '2022-04-01',
                'expiry_date' => '2027-03-31',
                'issuer' => 'District Commissioner Office, Rajshahi',
                'verified_date' => '2026-08-12',
            ],
            [
                'id' => 7,
                'name' => 'Nasrin Sultana',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-4419',
                'item_type' => 'Target Pistol (Walther PPK)',
                'bore' => '.22 LR',
                'quantity' => 1,
                'status' => 'Active',
                'nid_trade' => '1982345678901',
                'district' => 'Khulna',
                'thana' => 'Sadar',
                'address' => '45 KDA Avenue, Sadar, Khulna',
                'issue_date' => '2024-02-14',
                'expiry_date' => '2029-02-13',
                'issuer' => 'District Commissioner Office, Khulna',
                'verified_date' => '2026-08-25',
            ],
            [
                'id' => 8,
                'name' => 'Chittagong Gun Store',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0412',
                'item_type' => 'Sporting Rifle (Ruger M77)',
                'bore' => '.223 Rem',
                'quantity' => 15,
                'status' => 'In Stock',
                'nid_trade' => 'TR-CTG-2023-8871',
                'district' => 'Chattogram',
                'thana' => 'Panchlaish',
                'address' => '102 CDA Avenue, Panchlaish, Chattogram',
                'issue_date' => '2023-08-10',
                'expiry_date' => '2028-08-09',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-19',
            ],
        ];
    }

    public static function getAmmunitionData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Brig. Gen. (Retd.) Tariq Mahmud',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-8841',
                'item_type' => '9mm Parabellum Ammunition',
                'bore' => '9mm',
                'quantity' => 100,
                'status' => 'Active',
                'nid_trade' => '1965882910482',
                'district' => 'Dhaka',
                'thana' => 'Gulshan',
                'address' => 'House 42, Road 11, Block D, Gulshan-1, Dhaka',
                'issue_date' => '2022-03-15',
                'expiry_date' => '2027-03-14',
                'issuer' => 'Ministry of Home Affairs (MoHA)',
                'verified_date' => '2026-08-20',
            ],
            [
                'id' => 2,
                'name' => 'Dhaka Arms & Co.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0104',
                'item_type' => '12 Gauge Shotgun Shells',
                'bore' => '12 Gauge',
                'quantity' => 1800,
                'status' => 'In Stock',
                'nid_trade' => 'TR-DH-2021-9940',
                'district' => 'Dhaka',
                'thana' => 'Motijheel',
                'address' => '14 Toyenbee Circular Road, Motijheel, Dhaka',
                'issue_date' => '2020-01-10',
                'expiry_date' => '2028-01-09',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-15',
            ],
            [
                'id' => 3,
                'name' => 'Md. Rafiqul Islam',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-1042',
                'item_type' => '.308 Winchester Cartridges',
                'bore' => '.308 Win',
                'quantity' => 50,
                'status' => 'Active',
                'nid_trade' => '1991234567890',
                'district' => 'Dhaka',
                'thana' => 'Dhanmondi',
                'address' => 'Flat 5B, House 12, Road 5A, Dhanmondi, Dhaka',
                'issue_date' => '2023-06-20',
                'expiry_date' => '2028-06-19',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-10',
            ],
            [
                'id' => 4,
                'name' => 'Messrs Armoury House Ltd.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0218',
                'item_type' => '.32 ACP Handgun Ammo',
                'bore' => '.32 ACP',
                'quantity' => 1500,
                'status' => 'In Stock',
                'nid_trade' => 'TR-CTG-2019-4412',
                'district' => 'Chattogram',
                'thana' => 'Kotwali',
                'address' => '88 Station Road, Kotwali, Chattogram',
                'issue_date' => '2019-11-05',
                'expiry_date' => '2027-11-04',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-22',
            ],
            [
                'id' => 5,
                'name' => 'Fahim Ahmed Chowdhury',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-3310',
                'item_type' => '12 Gauge Target Loads',
                'bore' => '12 Gauge',
                'quantity' => 50,
                'status' => 'Active',
                'nid_trade' => '1975678901234',
                'district' => 'Sylhet',
                'thana' => 'Sadarganj',
                'address' => 'Chowdhury Bari, Zinda Bazar, Sylhet',
                'issue_date' => '2021-09-12',
                'expiry_date' => '2026-09-11',
                'issuer' => 'District Commissioner Office, Sylhet',
                'verified_date' => '2026-08-18',
            ],
            [
                'id' => 6,
                'name' => 'Bengal Sports & Arms',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0355',
                'item_type' => '9mm FMJ Ammunition',
                'bore' => '9mm',
                'quantity' => 1000,
                'status' => 'In Stock',
                'nid_trade' => 'TR-RAJ-2022-1108',
                'district' => 'Rajshahi',
                'thana' => 'Boalia',
                'address' => '22 Saheb Bazar Road, Boalia, Rajshahi',
                'issue_date' => '2022-04-01',
                'expiry_date' => '2027-03-31',
                'issuer' => 'District Commissioner Office, Rajshahi',
                'verified_date' => '2026-08-12',
            ],
            [
                'id' => 7,
                'name' => 'Nasrin Sultana',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-4419',
                'item_type' => '.22 LR Rimfire Rounds',
                'bore' => '.22 LR',
                'quantity' => 100,
                'status' => 'Active',
                'nid_trade' => '1982345678901',
                'district' => 'Khulna',
                'thana' => 'Sadar',
                'address' => '45 KDA Avenue, Sadar, Khulna',
                'issue_date' => '2024-02-14',
                'expiry_date' => '2029-02-13',
                'issuer' => 'District Commissioner Office, Khulna',
                'verified_date' => '2026-08-25',
            ],
            [
                'id' => 8,
                'name' => 'Chittagong Gun Store',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0412',
                'item_type' => '.223 Remington Rifle Ammo',
                'bore' => '.223 Rem',
                'quantity' => 400,
                'status' => 'In Stock',
                'nid_trade' => 'TR-CTG-2023-8871',
                'district' => 'Chattogram',
                'thana' => 'Panchlaish',
                'address' => '102 CDA Avenue, Panchlaish, Chattogram',
                'issue_date' => '2023-08-10',
                'expiry_date' => '2028-08-09',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-19',
            ],
        ];
    }

    /**
     * Display list of Firearms holdings.
     */
    public function firearmsList(Request $request)
    {
        $items = collect(self::getFirearmsData());

        // Support filtering by search query or user_type if provided
        if ($search = $request->input('search')) {
            $items = $items->filter(fn ($item) => str_contains(strtolower($item['name']), strtolower($search)) ||
                str_contains(strtolower($item['reference']), strtolower($search)) ||
                str_contains(strtolower($item['item_type']), strtolower($search))
            );
        }

        if ($userType = $request->input('user_type')) {
            $items = $items->where('user_type', ucfirst(strtolower($userType)));
        }

        $totalCount = collect(self::getFirearmsData())->sum('quantity');
        $citizenCount = collect(self::getFirearmsData())->where('user_type', 'Citizen')->sum('quantity');
        $dealerCount = collect(self::getFirearmsData())->where('user_type', 'Dealer')->sum('quantity');

        return view('overview.firearms', compact('items', 'totalCount', 'citizenCount', 'dealerCount'));
    }

    /**
     * Display list of Ammunition holdings.
     */
    public function ammunitionList(Request $request)
    {
        $items = collect(self::getAmmunitionData());

        // Support filtering by search query or user_type if provided
        if ($search = $request->input('search')) {
            $items = $items->filter(fn ($item) => str_contains(strtolower($item['name']), strtolower($search)) ||
                str_contains(strtolower($item['reference']), strtolower($search)) ||
                str_contains(strtolower($item['item_type']), strtolower($search))
            );
        }

        if ($userType = $request->input('user_type')) {
            $items = $items->where('user_type', ucfirst(strtolower($userType)));
        }

        $totalCount = collect(self::getAmmunitionData())->sum('quantity');
        $citizenCount = collect(self::getAmmunitionData())->where('user_type', 'Citizen')->sum('quantity');
        $dealerCount = collect(self::getAmmunitionData())->where('user_type', 'Dealer')->sum('quantity');

        return view('overview.ammunition', compact('items', 'totalCount', 'citizenCount', 'dealerCount'));
    }

    /**
     * Get the consistent dummy dataset for Licenses (Total: 14, Approved: 8, Pending: 4, Suspended: 2).
     */
    public static function getLicensesData(): array
    {
        return [
            [
                'id' => 1,
                'license_number' => 'BD-HND-DHK-004521',
                'name' => 'Brig. Gen. (Retd.) Tariq Mahmud',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-8841',
                'weapon_type' => 'Pistol (Glock 17)',
                'bore' => '9mm',
                'district' => 'Dhaka',
                'thana' => 'Gulshan',
                'address' => 'House 42, Road 11, Block D, Gulshan-1, Dhaka',
                'issue_date' => '2022-03-15',
                'expiry_date' => '2027-03-14',
                'status' => 'Approved',
                'status_note' => 'Active and fully compliant license holder',
                'issuer' => 'Ministry of Home Affairs (MoHA)',
                'verified_date' => '2026-08-20',
            ],
            [
                'id' => 2,
                'license_number' => 'DEAL-DHK-2024-001',
                'name' => 'Dhaka Arms & Co.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0104',
                'weapon_type' => 'Commercial Dealership (Class A)',
                'bore' => 'Multi-Calibre',
                'district' => 'Dhaka',
                'thana' => 'Motijheel',
                'address' => '14 Toyenbee Circular Road, Motijheel, Dhaka',
                'issue_date' => '2020-01-10',
                'expiry_date' => '2028-01-09',
                'status' => 'Approved',
                'status_note' => 'Licensed Class A Commercial Stockist',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-15',
            ],
            [
                'id' => 3,
                'license_number' => 'BD-LNG-DHK-001042',
                'name' => 'Md. Rafiqul Islam',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-1042',
                'weapon_type' => 'Hunting Rifle (Winchester 70)',
                'bore' => '.308 Win',
                'district' => 'Dhaka',
                'thana' => 'Dhanmondi',
                'address' => 'Flat 5B, House 12, Road 5A, Dhanmondi, Dhaka',
                'issue_date' => '2023-06-20',
                'expiry_date' => '2028-06-19',
                'status' => 'Approved',
                'status_note' => 'Active license for personal hunting & sporting',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-10',
            ],
            [
                'id' => 4,
                'license_number' => 'DEAL-CTG-2023-002',
                'name' => 'Messrs Armoury House Ltd.',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0218',
                'weapon_type' => 'Wholesale Dealership (Class B)',
                'bore' => 'Handguns & Ammo',
                'district' => 'Chattogram',
                'thana' => 'Kotwali',
                'address' => '88 Station Road, Kotwali, Chattogram',
                'issue_date' => '2019-11-05',
                'expiry_date' => '2027-11-04',
                'status' => 'Approved',
                'status_note' => 'Licensed Class B Wholesale Dealer',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-22',
            ],
            [
                'id' => 5,
                'license_number' => 'BD-LNG-SYL-003310',
                'name' => 'Fahim Ahmed Chowdhury',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-3310',
                'weapon_type' => 'Double Barrel Shotgun',
                'bore' => '12 Gauge',
                'district' => 'Sylhet',
                'thana' => 'Sadarganj',
                'address' => 'Chowdhury Bari, Zinda Bazar, Sylhet',
                'issue_date' => '2021-09-12',
                'expiry_date' => '2026-09-11',
                'status' => 'Approved',
                'status_note' => 'Active estate protection firearm license',
                'issuer' => 'District Commissioner Office, Sylhet',
                'verified_date' => '2026-08-18',
            ],
            [
                'id' => 6,
                'license_number' => 'DEAL-RAJ-2024-003',
                'name' => 'Bengal Sports & Arms',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0355',
                'weapon_type' => 'Retail Dealership (Class C)',
                'bore' => 'Pistols & Rifles',
                'district' => 'Rajshahi',
                'thana' => 'Boalia',
                'address' => '22 Saheb Bazar Road, Boalia, Rajshahi',
                'issue_date' => '2022-04-01',
                'expiry_date' => '2027-03-31',
                'status' => 'Approved',
                'status_note' => 'Licensed Regional Retail Stockist',
                'issuer' => 'District Commissioner Office, Rajshahi',
                'verified_date' => '2026-08-12',
            ],
            [
                'id' => 7,
                'license_number' => 'BD-HND-KLN-004419',
                'name' => 'Nasrin Sultana',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-4419',
                'weapon_type' => 'Target Pistol (Walther PPK)',
                'bore' => '.22 LR',
                'district' => 'Khulna',
                'thana' => 'Sadar',
                'address' => '45 KDA Avenue, Sadar, Khulna',
                'issue_date' => '2024-02-14',
                'expiry_date' => '2029-02-13',
                'status' => 'Approved',
                'status_note' => 'Active competitive sport firearm license',
                'issuer' => 'District Commissioner Office, Khulna',
                'verified_date' => '2026-08-25',
            ],
            [
                'id' => 8,
                'license_number' => 'DEAL-CTG-2025-004',
                'name' => 'Chittagong Gun Store',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0412',
                'weapon_type' => 'Retail Dealership (Class C)',
                'bore' => 'Sporting Rifles',
                'district' => 'Chattogram',
                'thana' => 'Panchlaish',
                'address' => '102 CDA Avenue, Panchlaish, Chattogram',
                'issue_date' => '2023-08-10',
                'expiry_date' => '2028-08-09',
                'status' => 'Approved',
                'status_note' => 'Licensed Retail Arms & Ammo Dealer',
                'issuer' => 'District Commissioner Office, Chattogram',
                'verified_date' => '2026-08-19',
            ],
            [
                'id' => 9,
                'license_number' => 'BD-PEN-BAR-005120',
                'name' => 'Salma Begum',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-5120',
                'weapon_type' => 'Shotgun (12 Bore)',
                'bore' => '12 Gauge',
                'district' => 'Barisal',
                'thana' => 'Kotwali',
                'address' => 'Band Road, Kotwali, Barisal',
                'issue_date' => '—',
                'expiry_date' => '—',
                'status' => 'Pending',
                'status_note' => 'Police & SB field vetting clearance in progress',
                'issuer' => 'Under Review (DC Barisal)',
                'verified_date' => '2026-08-28',
            ],
            [
                'id' => 10,
                'license_number' => 'BD-PEN-RNG-006231',
                'name' => 'Kamal Hossain',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-6231',
                'weapon_type' => 'Pistol (.32 ACP)',
                'bore' => '.32 Calibre',
                'district' => 'Rangpur',
                'thana' => 'Sadar',
                'address' => 'Station Road, Sadar, Rangpur',
                'issue_date' => '—',
                'expiry_date' => '—',
                'status' => 'Pending',
                'status_note' => 'Awaiting final approval from DC JM Branch',
                'issuer' => 'Under Review (DC Rangpur)',
                'verified_date' => '2026-08-27',
            ],
            [
                'id' => 11,
                'license_number' => 'DEAL-PEN-MYM-00520',
                'name' => 'Padma Armoury & Security',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0520',
                'weapon_type' => 'Wholesale Dealership (Class B)',
                'bore' => 'All Calibres',
                'district' => 'Mymensingh',
                'thana' => 'Sadar',
                'address' => '5 Station Road, Sadar, Mymensingh',
                'issue_date' => '—',
                'expiry_date' => '—',
                'status' => 'Pending',
                'status_note' => 'Referred to MoHA National Screening Committee',
                'issuer' => 'Under Review (MoHA)',
                'verified_date' => '2026-08-29',
            ],
            [
                'id' => 12,
                'license_number' => 'BD-PEN-DHK-007342',
                'name' => 'Roksana Akter',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-7342',
                'weapon_type' => 'Sporting Rifle (.22 Bore)',
                'bore' => '.22 Bore',
                'district' => 'Dhaka',
                'thana' => 'Mirpur',
                'address' => 'Section 10, Block C, Mirpur, Dhaka',
                'issue_date' => '—',
                'expiry_date' => '—',
                'status' => 'Pending',
                'status_note' => 'Under Joint Intelligence DGFI/NSI review',
                'issuer' => 'Under Review (DC Dhaka)',
                'verified_date' => '2026-08-30',
            ],
            [
                'id' => 13,
                'license_number' => 'BD-SUS-DHK-009011',
                'name' => 'Abul Kashem Mia',
                'user_type' => 'Citizen',
                'reference' => 'NFL-2026-9011',
                'weapon_type' => 'Revolver (.32 Calibre)',
                'bore' => '.32 Calibre',
                'district' => 'Dhaka',
                'thana' => 'Uttara',
                'address' => 'Sector 4, Road 7, Uttara, Dhaka',
                'issue_date' => '2021-05-10',
                'expiry_date' => '2026-05-09',
                'status' => 'Suspended',
                'status_note' => 'Arms Act §17 non-compliance inquiry hold',
                'issuer' => 'District Commissioner Office, Dhaka',
                'verified_date' => '2026-08-14',
            ],
            [
                'id' => 14,
                'license_number' => 'DEAL-SUS-CTG-00601',
                'name' => 'National Arms Agency',
                'user_type' => 'Dealer',
                'reference' => 'DLR-2026-0601',
                'weapon_type' => 'Retail Dealership (Class C)',
                'bore' => 'Handgun & Long Gun',
                'district' => 'Chattogram',
                'thana' => 'Panchlaish',
                'address' => 'GEC Circle, Panchlaish, Chattogram',
                'issue_date' => '2020-08-15',
                'expiry_date' => '2025-08-14',
                'status' => 'Suspended',
                'status_note' => 'Suspended pending reconciliation of stock audit',
                'issuer' => 'Ministry of Home Affairs (MoHA)',
                'verified_date' => '2026-08-17',
            ],
        ];
    }

    /**
     * Get the consistent dummy dataset for Citizens (Total: 6).
     */
    public static function getCitizensData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Brig. Gen. (Retd.) Tariq Mahmud',
                'nid' => '1965882910482',
                'email' => 'tariq.mahmud@gmail.com',
                'phone' => '+880 1711-884102',
                'district' => 'Dhaka',
                'thana' => 'Gulshan',
                'address' => 'House 42, Road 11, Block D, Gulshan-1, Dhaka',
                'occupation' => 'Defense Veteran / Security Consultant',
                'annual_income' => 2400000,
                'licensed_weapon' => 'Pistol (Glock 17, 9mm)',
                'bore' => '9mm',
                'total_firearms' => 2,
                'status' => 'Active',
                'registration_date' => '2022-03-15',
            ],
            [
                'id' => 2,
                'name' => 'Md. Rafiqul Islam',
                'nid' => '1991234567890',
                'email' => 'rafiqul.islam@yahoo.com',
                'phone' => '+880 1819-204192',
                'district' => 'Dhaka',
                'thana' => 'Dhanmondi',
                'address' => 'Flat 5B, House 12, Road 5A, Dhanmondi, Dhaka',
                'occupation' => 'Industrialist / Exporter',
                'annual_income' => 5500000,
                'licensed_weapon' => 'Hunting Rifle (Winchester 70)',
                'bore' => '.308 Win',
                'total_firearms' => 1,
                'status' => 'Active',
                'registration_date' => '2023-06-20',
            ],
            [
                'id' => 3,
                'name' => 'Fahim Ahmed Chowdhury',
                'nid' => '1975678901234',
                'email' => 'fahim.chy@outlook.com',
                'phone' => '+880 1712-331045',
                'district' => 'Sylhet',
                'thana' => 'Sadarganj',
                'address' => 'Chowdhury Bari, Zinda Bazar, Sylhet',
                'occupation' => 'Tea Estate Planter',
                'annual_income' => 3800000,
                'licensed_weapon' => 'Double Barrel Shotgun',
                'bore' => '12 Gauge',
                'total_firearms' => 1,
                'status' => 'Active',
                'registration_date' => '2021-09-12',
            ],
            [
                'id' => 4,
                'name' => 'Nasrin Sultana',
                'nid' => '1982345678901',
                'email' => 'nasrin.sultana@gmail.com',
                'phone' => '+880 1914-441920',
                'district' => 'Khulna',
                'thana' => 'Sadar',
                'address' => '45 KDA Avenue, Sadar, Khulna',
                'occupation' => 'National Shooting Athlete',
                'annual_income' => 1800000,
                'licensed_weapon' => 'Target Pistol (Walther PPK)',
                'bore' => '.22 LR',
                'total_firearms' => 1,
                'status' => 'Active',
                'registration_date' => '2024-02-14',
            ],
            [
                'id' => 5,
                'name' => 'Salma Begum',
                'nid' => '1980123456789',
                'email' => 'salma.begum@gmail.com',
                'phone' => '+880 1715-512033',
                'district' => 'Barisal',
                'thana' => 'Kotwali',
                'address' => 'Band Road, Kotwali, Barisal',
                'occupation' => 'Landowner & Agro Business',
                'annual_income' => 1500000,
                'licensed_weapon' => 'Shotgun (12 Bore)',
                'bore' => '12 Gauge',
                'total_firearms' => 0,
                'status' => 'Pending Verification',
                'registration_date' => '2026-08-28',
            ],
            [
                'id' => 6,
                'name' => 'Kamal Hossain',
                'nid' => '1978901234567',
                'email' => 'kamal.hossain@gmail.com',
                'phone' => '+880 1816-623199',
                'district' => 'Rangpur',
                'thana' => 'Sadar',
                'address' => 'Station Road, Sadar, Rangpur',
                'occupation' => 'Cold Storage Owner',
                'annual_income' => 2100000,
                'licensed_weapon' => 'Pistol (.32 ACP)',
                'bore' => '.32 Calibre',
                'total_firearms' => 0,
                'status' => 'Pending Verification',
                'registration_date' => '2026-08-27',
            ],
        ];
    }

    /**
     * Get the consistent dummy dataset for Dealers (Total: 5).
     */
    public static function getDealersData(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Dhaka Arms & Co.',
                'trade_license' => 'TR-DH-2021-9940',
                'proprietor' => 'Alhaj M. A. Rahman',
                'email' => 'info@dhakaarms.com',
                'phone' => '+880 2-9568412',
                'district' => 'Dhaka',
                'thana' => 'Motijheel',
                'address' => '14 Toyenbee Circular Road, Motijheel, Dhaka',
                'dealer_class' => 'Class A — Manufacture & Dealing',
                'total_firearms' => 35,
                'total_ammo' => 1800,
                'total_stock' => 1835,
                'status' => 'Active',
                'issue_date' => '2020-01-10',
                'expiry_date' => '2028-01-09',
            ],
            [
                'id' => 2,
                'name' => 'Messrs Armoury House Ltd.',
                'trade_license' => 'TR-CTG-2019-4412',
                'proprietor' => 'Engr. S. K. Sen',
                'email' => 'sales@armouryhouse.com',
                'phone' => '+880 31-618420',
                'district' => 'Chattogram',
                'thana' => 'Kotwali',
                'address' => '88 Station Road, Kotwali, Chattogram',
                'dealer_class' => 'Class B — Wholesale Dealing',
                'total_firearms' => 40,
                'total_ammo' => 1500,
                'total_stock' => 1540,
                'status' => 'Active',
                'issue_date' => '2019-11-05',
                'expiry_date' => '2027-11-04',
            ],
            [
                'id' => 3,
                'name' => 'Bengal Sports & Arms',
                'trade_license' => 'TR-RAJ-2022-1108',
                'proprietor' => 'Md. Anisur Rahman',
                'email' => 'contact@bengalarms.com',
                'phone' => '+880 721-774512',
                'district' => 'Rajshahi',
                'thana' => 'Boalia',
                'address' => '22 Saheb Bazar Road, Boalia, Rajshahi',
                'dealer_class' => 'Class C — Retail Dealing',
                'total_firearms' => 25,
                'total_ammo' => 1000,
                'total_stock' => 1025,
                'status' => 'Active',
                'issue_date' => '2022-04-01',
                'expiry_date' => '2027-03-31',
            ],
            [
                'id' => 4,
                'name' => 'Chittagong Gun Store',
                'trade_license' => 'TR-CTG-2023-8871',
                'proprietor' => 'Nurul Huda',
                'email' => 'ctg.gunstore@gmail.com',
                'phone' => '+880 31-255102',
                'district' => 'Chattogram',
                'thana' => 'Panchlaish',
                'address' => '102 CDA Avenue, Panchlaish, Chattogram',
                'dealer_class' => 'Class C — Retail Dealing',
                'total_firearms' => 15,
                'total_ammo' => 400,
                'total_stock' => 415,
                'status' => 'Active',
                'issue_date' => '2023-08-10',
                'expiry_date' => '2028-08-09',
            ],
            [
                'id' => 5,
                'name' => 'Padma Armoury & Security',
                'trade_license' => 'TR-MYM-2024-0312',
                'proprietor' => 'Kazi Faruk Ahmed',
                'email' => 'padma.arms@gmail.com',
                'phone' => '+880 91-65481',
                'district' => 'Mymensingh',
                'thana' => 'Sadar',
                'address' => '5 Station Road, Sadar, Mymensingh',
                'dealer_class' => 'Class B — Wholesale Dealing',
                'total_firearms' => 5,
                'total_ammo' => 300,
                'total_stock' => 305,
                'status' => 'Pending Renewal',
                'issue_date' => '2021-10-01',
                'expiry_date' => '2026-09-30',
            ],
        ];
    }

    /**
     * Paginate a collection with a clean LengthAwarePaginator.
     */
    protected function paginateCollection(Collection $items, Request $request, int $defaultPerPage = 10): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        if ($perPage <= 0) {
            $perPage = $defaultPerPage;
        }
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }

    /**
     * Display list of Licenses (All or filtered by status).
     */
    public function licensesList(Request $request, ?string $presetStatus = null)
    {
        $raw = collect(self::getLicensesData());

        $statusFilter = $presetStatus ?? $request->input('status');

        $filtered = $raw;
        if (! empty($statusFilter) && strtolower($statusFilter) !== 'all') {
            $filtered = $filtered->filter(fn ($item) => strtolower($item['status']) === strtolower($statusFilter));
        }

        if ($search = $request->input('search')) {
            $s = strtolower($search);
            $filtered = $filtered->filter(fn ($item) => str_contains(strtolower($item['name']), $s) ||
                str_contains(strtolower($item['license_number']), $s) ||
                str_contains(strtolower($item['weapon_type']), $s) ||
                str_contains(strtolower($item['district']), $s)
            );
        }

        if ($userType = $request->input('user_type')) {
            $filtered = $filtered->where('user_type', ucfirst(strtolower($userType)));
        }

        $totalCount = $raw->count();
        $approvedCount = $raw->where('status', 'Approved')->count();
        $pendingCount = $raw->where('status', 'Pending')->count();
        $suspendedCount = $raw->where('status', 'Suspended')->count();

        $pageTitle = match (strtolower((string) $statusFilter)) {
            'approved' => 'Approved Licenses List',
            'pending' => 'Pending Licenses List',
            'suspended' => 'Suspended Licenses List',
            default => 'Total Licenses Overview',
        };

        $pageSubtitle = match (strtolower((string) $statusFilter)) {
            'approved' => 'Official registry of authorized active firearm licenses',
            'pending' => 'Applications undergoing security clearance, vetting & approvals',
            'suspended' => 'Licenses temporarily flagged or non-compliant under regulatory review',
            default => 'Comprehensive national register of all approved, pending & suspended firearm licenses',
        };

        $items = $this->paginateCollection($filtered, $request, 10);

        return view('overview.licenses', compact(
            'items',
            'totalCount',
            'approvedCount',
            'pendingCount',
            'suspendedCount',
            'statusFilter',
            'pageTitle',
            'pageSubtitle'
        ));
    }

    /**
     * Approved Licenses dedicated list.
     */
    public function approvedLicensesList(Request $request)
    {
        return $this->licensesList($request, 'approved');
    }

    /**
     * Pending Licenses dedicated list.
     */
    public function pendingLicensesList(Request $request)
    {
        return $this->licensesList($request, 'pending');
    }

    /**
     * Suspended Licenses dedicated list.
     */
    public function suspendedLicensesList(Request $request)
    {
        return $this->licensesList($request, 'suspended');
    }

    /**
     * Display list of Citizen licensees.
     */
    public function citizensList(Request $request)
    {
        $raw = collect(self::getCitizensData());

        $filtered = $raw;
        if ($search = $request->input('search')) {
            $s = strtolower($search);
            $filtered = $filtered->filter(fn ($item) => str_contains(strtolower($item['name']), $s) ||
                str_contains(strtolower($item['nid']), $s) ||
                str_contains(strtolower($item['licensed_weapon']), $s) ||
                str_contains(strtolower($item['district']), $s)
            );
        }

        if ($district = $request->input('district')) {
            $filtered = $filtered->where('district', $district);
        }

        $totalCitizens = $raw->count();
        $activeCitizens = $raw->where('status', 'Active')->count();
        $totalFirearmsHeld = $raw->sum('total_firearms');

        $items = $this->paginateCollection($filtered, $request, 4);

        return view('overview.citizens', compact(
            'items',
            'totalCitizens',
            'activeCitizens',
            'totalFirearmsHeld'
        ));
    }

    /**
     * Display list of Licensed Commercial Arms Dealers.
     */
    public function dealersList(Request $request)
    {
        $raw = collect(self::getDealersData());

        $filtered = $raw;
        if ($search = $request->input('search')) {
            $s = strtolower($search);
            $filtered = $filtered->filter(fn ($item) => str_contains(strtolower($item['name']), $s) ||
                str_contains(strtolower($item['trade_license']), $s) ||
                str_contains(strtolower($item['proprietor']), $s) ||
                str_contains(strtolower($item['district']), $s)
            );
        }

        if ($district = $request->input('district')) {
            $filtered = $filtered->where('district', $district);
        }

        $totalDealers = $raw->count();
        $totalArmsInStock = $raw->sum('total_firearms');
        $totalAmmoInStock = $raw->sum('total_ammo');

        $items = $this->paginateCollection($filtered, $request, 3);

        return view('overview.dealers', compact(
            'items',
            'totalDealers',
            'totalArmsInStock',
            'totalAmmoInStock'
        ));
    }

    /**
     * Display detailed dossier for a specific License.
     */
    public function licenseShow(Request $request, int $id)
    {
        $licenses = collect(self::getLicensesData());
        $license = $licenses->firstWhere('id', (int) $id);

        if (! $license) {
            abort(404, 'Firearm license record not found.');
        }

        $from = $request->input('from');
        $backRoute = match (strtolower((string) $from)) {
            'approved' => route('overview.licenses.approved'),
            'pending' => route('overview.licenses.pending'),
            'suspended' => route('overview.licenses.suspended'),
            default => route('overview.licenses'),
        };

        return view('overview.license_detail', compact('license', 'backRoute'));
    }

    /**
     * Display detailed profile for a specific Citizen licensee.
     */
    public function citizenShow(Request $request, int $id)
    {
        $citizens = collect(self::getCitizensData());
        $citizen = $citizens->firstWhere('id', (int) $id);

        if (! $citizen) {
            abort(404, 'Citizen record not found.');
        }

        $backRoute = route('overview.citizens');

        return view('overview.citizen_detail', compact('citizen', 'backRoute'));
    }

    /**
     * Display detailed registry dossier for a specific Arms Dealer.
     */
    public function dealerShow(Request $request, int $id)
    {
        $dealers = collect(self::getDealersData());
        $dealer = $dealers->firstWhere('id', (int) $id);

        if (! $dealer) {
            abort(404, 'Commercial arms dealer record not found.');
        }

        $backRoute = route('overview.dealers');

        return view('overview.dealer_detail', compact('dealer', 'backRoute'));
    }
}
