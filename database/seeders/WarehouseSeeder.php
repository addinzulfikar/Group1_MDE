<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // warehouse_code column has been removed; status aligned with hubs (available/full/overload)
        $warehouses = [
            ['name' => 'Banda Aceh Hub',                   'location' => 'Banda Aceh',          'capacity' => 17530, 'status' => 'available'],
            ['name' => 'Ternate Hub',                      'location' => 'Ternate',              'capacity' => 6803,  'status' => 'available'],
            ['name' => 'Surabaya Hub',                     'location' => 'Surabaya',             'capacity' => 14994, 'status' => 'available'],
            ['name' => 'Batam Hub',                        'location' => 'Batam',                'capacity' => 16341, 'status' => 'available'],
            ['name' => 'Balikpapan Hub',                   'location' => 'Balikpapan',           'capacity' => 8748,  'status' => 'available'],
            ['name' => 'Banjarmasin Hub',                  'location' => 'Banjarmasin',          'capacity' => 10169, 'status' => 'available'],
            ['name' => 'Blitar Hub',                       'location' => 'Blitar',               'capacity' => 16394, 'status' => 'available'],
            ['name' => 'Jakarta Barat Hub',                'location' => 'Jakarta Barat',        'capacity' => 19209, 'status' => 'available'],
            ['name' => 'Serang Hub',                       'location' => 'Serang',               'capacity' => 14330, 'status' => 'available'],
            ['name' => 'Jakarta Timur Hub',                'location' => 'Jakarta Timur',        'capacity' => 14996, 'status' => 'available'],
            ['name' => 'Sibolga Hub',                      'location' => 'Sibolga',              'capacity' => 11703, 'status' => 'available'],
            ['name' => 'Jakarta Selatan Hub',              'location' => 'Jakarta Selatan',      'capacity' => 18061, 'status' => 'available'],
            ['name' => 'Mataram Hub',                      'location' => 'Mataram',              'capacity' => 14340, 'status' => 'available'],
            ['name' => 'Bandar Lampung Hub',               'location' => 'Bandar Lampung',       'capacity' => 15096, 'status' => 'available'],
            ['name' => 'Tangerang Hub',                    'location' => 'Tangerang',            'capacity' => 9852,  'status' => 'available'],
            ['name' => 'Magelang Hub',                     'location' => 'Magelang',             'capacity' => 9116,  'status' => 'available'],
            ['name' => 'Tarakan Hub',                      'location' => 'Tarakan',              'capacity' => 19436, 'status' => 'available'],
            ['name' => 'Sorong Hub',                       'location' => 'Sorong',               'capacity' => 5891,  'status' => 'available'],
            ['name' => 'Lubuklinggau Hub',                 'location' => 'Lubuklinggau',         'capacity' => 16244, 'status' => 'available'],
            ['name' => 'Padangpanjang Hub',                'location' => 'Padangpanjang',        'capacity' => 7042,  'status' => 'available'],
            ['name' => 'Manado Hub',                       'location' => 'Manado',               'capacity' => 19688, 'status' => 'available'],
            ['name' => 'Pematangsiantar Hub',              'location' => 'Pematangsiantar',      'capacity' => 7854,  'status' => 'available'],
            ['name' => 'Sukabumi Hub',                     'location' => 'Sukabumi',             'capacity' => 16047, 'status' => 'available'],
            ['name' => 'Palembang Hub',                    'location' => 'Palembang',            'capacity' => 12566, 'status' => 'available'],
            ['name' => 'Jayapura Hub',                     'location' => 'Jayapura',             'capacity' => 17645, 'status' => 'available'],
            ['name' => 'Manado Utara Hub',                 'location' => 'Manado',               'capacity' => 5547,  'status' => 'available'],
            ['name' => 'Tegal Hub',                        'location' => 'Tegal',                'capacity' => 19677, 'status' => 'available'],
            ['name' => 'Madiun Hub',                       'location' => 'Madiun',               'capacity' => 7314,  'status' => 'available'],
            ['name' => 'Bengkulu Hub',                     'location' => 'Bengkulu',             'capacity' => 12583, 'status' => 'available'],
            ['name' => 'Bandung Hub',                      'location' => 'Bandung',              'capacity' => 7130,  'status' => 'available'],
            ['name' => 'Gorontalo Hub',                    'location' => 'Gorontalo',            'capacity' => 6147,  'status' => 'available'],
            ['name' => 'Solok Hub',                        'location' => 'Solok',                'capacity' => 18321, 'status' => 'available'],
            ['name' => 'Surabaya Selatan Hub',             'location' => 'Surabaya',             'capacity' => 11658, 'status' => 'available'],
            ['name' => 'Sabang Hub',                       'location' => 'Sabang',               'capacity' => 8421,  'status' => 'available'],
            ['name' => 'Dumai Hub',                        'location' => 'Dumai',                'capacity' => 7423,  'status' => 'available'],
            ['name' => 'Pekanbaru Hub',                    'location' => 'Pekanbaru',            'capacity' => 10441, 'status' => 'available'],
            ['name' => 'Bandar Lampung Utara Hub',         'location' => 'Bandar Lampung',       'capacity' => 5993,  'status' => 'available'],
            ['name' => 'Depok Hub',                        'location' => 'Depok',                'capacity' => 16542, 'status' => 'available'],
            ['name' => 'Padangpanjang Barat Hub',          'location' => 'Padangpanjang',        'capacity' => 7864,  'status' => 'available'],
            ['name' => 'Lubuklinggau Selatan Hub',         'location' => 'Lubuklinggau',         'capacity' => 7880,  'status' => 'available'],
            ['name' => 'Gunungsitoli Hub',                 'location' => 'Gunungsitoli',         'capacity' => 13716, 'status' => 'available'],
            ['name' => 'Malang Hub',                       'location' => 'Malang',               'capacity' => 12343, 'status' => 'available'],
            ['name' => 'Kupang Hub',                       'location' => 'Kupang',               'capacity' => 11456, 'status' => 'available'],
            ['name' => 'Cimahi Hub',                       'location' => 'Cimahi',               'capacity' => 10223, 'status' => 'available'],
            ['name' => 'Cirebon Hub',                      'location' => 'Cirebon',              'capacity' => 10087, 'status' => 'available'],
            ['name' => 'Bengkulu Utara Hub',               'location' => 'Bengkulu',             'capacity' => 10302, 'status' => 'available'],
            ['name' => 'Tirai Hub',                        'location' => 'Tirai',                'capacity' => 19288, 'status' => 'available'],
            ['name' => 'Bandung Barat Hub',                'location' => 'Bandung',              'capacity' => 14979, 'status' => 'available'],
            ['name' => 'Tual Hub',                         'location' => 'Tual',                 'capacity' => 13656, 'status' => 'available'],
            ['name' => 'Jayapura Selatan Hub',             'location' => 'Jayapura',             'capacity' => 11560, 'status' => 'available'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'warehouse_name' => $warehouse['name'],
                'location'       => $warehouse['location'],
                'capacity'       => $warehouse['capacity'],
                'current_load'   => 0,   // will be recalculated after PackageSeeder runs
                'status'         => $warehouse['status'],
            ]);
        }
    }
}
