<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
