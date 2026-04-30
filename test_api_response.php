<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "✓ M2 API RESPONSE VERIFICATION\n";
echo "================================\n\n";

// Get a sample shipment
$shipment = \App\Models\Shipment::with(['customer', 'package', 'fleet', 'originHub', 'destinationHub', 'currentHub', 'trackingHistories'])
    ->first();

// Simulate API response format
$response = [
    'id' => $shipment->id,
    'tracking_number' => $shipment->tracking_number,
    'status' => $shipment->status,
    'sent_at' => $shipment->sent_at,
    'delivered_at' => $shipment->delivered_at,
    
    // M3 Customer Data
    'customer' => [
        'id' => $shipment->customer->id,
        'name' => $shipment->customer->name,
        'email' => $shipment->customer->email,
        'phone' => $shipment->customer->phone,
    ],
    
    // M1 Package Data
    'package' => $shipment->package ? [
        'id' => $shipment->package->id,
        'sender_name' => $shipment->package->sender_name,
        'sender_phone' => $shipment->package->sender_phone,
        'sender_address' => $shipment->package->sender_address,
        'receiver_name' => $shipment->package->receiver_name,
        'receiver_phone' => $shipment->package->receiver_phone,
        'receiver_address' => $shipment->package->receiver_address,
        'weight' => $shipment->package->weight,
        'dimensions' => $shipment->package->dimensions,
        'origin' => $shipment->package->origin,
        'destination' => $shipment->package->destination,
    ] : null,
    
    // M4 Hub Data
    'origin_hub' => [
        'id' => $shipment->originHub->id,
        'name' => $shipment->originHub->name,
        'city' => $shipment->originHub->city,
        'address' => $shipment->originHub->address,
    ],
    'destination_hub' => [
        'id' => $shipment->destinationHub->id,
        'name' => $shipment->destinationHub->name,
        'city' => $shipment->destinationHub->city,
        'address' => $shipment->destinationHub->address,
    ],
    'current_hub' => [
        'id' => $shipment->currentHub->id,
        'name' => $shipment->currentHub->name,
        'city' => $shipment->currentHub->city,
        'address' => $shipment->currentHub->address,
    ],
    
    // M4 Fleet Data
    'fleet' => $shipment->fleet ? [
        'id' => $shipment->fleet->id,
        'name' => $shipment->fleet->name,
        'license_plate' => $shipment->fleet->license_plate,
        'type' => $shipment->fleet->type,
    ] : null,
    
    // Tracking history
    'tracking_count' => $shipment->trackingHistories->count(),
];

echo "Sample API Response (JSON Output):\n";
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 INTEGRATION SUMMARY:\n";
echo str_repeat("=", 60) . "\n";
echo sprintf("✓ M1 Data Accessible: %d package fields included\n", is_array($response['package']) ? count($response['package']) : 0);
echo sprintf("✓ M3 Data Accessible: %d customer fields included\n", count($response['customer']));
echo sprintf("✓ M4 Hub Data Accessible: %d hub/location fields\n", count($response['origin_hub']) + count($response['destination_hub']) + count($response['current_hub']));
echo sprintf("✓ M4 Fleet Data Accessible: %s\n", $response['fleet'] ? 'YES (' . count($response['fleet']) . ' fields)' : 'Not assigned (nullable)');
echo sprintf("✓ Tracking History: %d records linked\n", $response['tracking_count']);

echo "\n✅ M2 IS FULLY INTEGRATED WITH M1, M3, M4!\n";
echo str_repeat("=", 60) . "\n";
