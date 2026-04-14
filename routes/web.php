<?php

use Illuminate\Support\Facades\Route;

use App\Repositories\Contracts\FleetRepositoryInterface;
use App\Repositories\Contracts\HubRepositoryInterface;

Route::get('/', function (\Illuminate\Http\Request $request, HubRepositoryInterface $hubRepo, FleetRepositoryInterface $fleetRepo) {
    if(!\Illuminate\Support\Facades\Schema::hasTable('hubs')) {
        return "Database sedang disiapkan, ini wajar saat instalasi. Harap refresh halaman.";
    }
    
    $hubs = $hubRepo->getAllHubs($request->search_hub);
    $allHubs = \App\Models\Hub::orderBy('name')->get();
    $fleets = $fleetRepo->getAllFleets($request->search_fleet); // returns pagination
    
    return view('dashboard', compact('hubs', 'allHubs', 'fleets'));
});
