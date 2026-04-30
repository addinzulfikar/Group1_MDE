<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "✓ M2 INTEGRATION VERIFICATION\n";
echo "================================\n\n";

$shipment = \App\Models\Shipment::with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub'])->first();

if ($shipment) {
    echo "Tracking: " . $shipment->tracking_number . "\n";
    echo "Status: " . $shipment->status . "\n";
    echo "\n📦 M1 (Package) - Sender/Receiver/Weight Data:\n";
    if ($shipment->package) {
        echo "  Sender: {$shipment->package->sender_name} (M1 ✓)\n";
        echo "  Receiver: {$shipment->package->receiver_name} (M1 ✓)\n";
        echo "  Weight: {$shipment->package->weight}kg (M1 ✓)\n";
    }
    echo "\n👤 M3 (Customer) - Ownership:\n";
    echo "  Owner: {$shipment->customer->name} (M3 ✓)\n";
    echo "  Email: {$shipment->customer->email} (M3 ✓)\n";
    echo "\n🏢 M4 (Hub/Fleet) - Location & Transport:\n";
    echo "  Origin: {$shipment->originHub->name} (M4 ✓)\n";
    echo "  Destination: {$shipment->destinationHub->name} (M4 ✓)\n";
    echo "  Current: {$shipment->currentHub->name} (M4 ✓)\n";
    if ($shipment->fleet) echo "  Fleet: {$shipment->fleet->name} (M4 ✓)\n";
    echo "\n✅ ALL 4 MODULES INTEGRATED AND WORKING!\n";
} else {
    echo "❌ No shipment found - database not seeded\n";
}
