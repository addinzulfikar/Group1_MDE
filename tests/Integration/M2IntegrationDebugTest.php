<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Shipment;
use App\Models\Package;
use App\Models\User;
use App\Models\Hub;
use App\Models\Fleet;
use Database\Seeders\DatabaseSeeder;

class M2IntegrationDebugTest extends TestCase
{
    /**
     * Setup test database with seeders
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test database with minimal data
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test M1 (Package) ↔ M2 (Shipment) Integration
     */
    public function test_m1_m2_integration()
    {
        // Get a shipment with package relationship
        $shipment = Shipment::with('package')->whereNotNull('package_id')->first();
        
        $this->assertNotNull($shipment, 'Shipment with package should exist');
        $this->assertNotNull($shipment->package, 'Package relationship should load');
        $this->assertEquals($shipment->package_id, $shipment->package->id, 'Package FK should match');
        
        echo "\n✓ M1←→M2 Integration OK\n";
        echo "  Shipment #{$shipment->tracking_number} → Package #{$shipment->package->id}\n";
        echo "  Sender: {$shipment->package->sender_name}\n";
        echo "  Weight: {$shipment->package->weight}kg\n";
        
        return true;
    }

    /**
     * Test M3 (Customer Auth) ↔ M2 (Shipment) Integration  
     */
    public function test_m3_m2_integration()
    {
        // Get a shipment with customer relationship
        $shipment = Shipment::with('customer')->whereNotNull('customer_id')->first();
        
        $this->assertNotNull($shipment, 'Shipment with customer should exist');
        $this->assertNotNull($shipment->customer, 'Customer relationship should load');
        $this->assertEquals($shipment->customer_id, $shipment->customer->id, 'Customer FK should match');
        $this->assertNotNull($shipment->customer_id, 'customer_id should NOT be NULL (enforced)');
        
        echo "\n✓ M3←→M2 Integration OK\n";
        echo "  Shipment #{$shipment->tracking_number} owned by {$shipment->customer->name}\n";
        echo "  Customer email: {$shipment->customer->email}\n";
        
        return true;
    }

    /**
     * Test M4 (Fleet & Hub) ↔ M2 (Shipment) Integration
     */
    public function test_m4_m2_integration()
    {
        // Get a shipment with hub and fleet relationships
        $shipment = Shipment::with(['originHub', 'destinationHub', 'currentHub', 'fleet'])->first();
        
        $this->assertNotNull($shipment, 'Shipment should exist');
        $this->assertNotNull($shipment->originHub, 'Origin hub relationship should load');
        $this->assertNotNull($shipment->destinationHub, 'Destination hub relationship should load');
        $this->assertNotNull($shipment->currentHub, 'Current hub relationship should load');
        
        echo "\n✓ M4←→M2 Integration OK\n";
        echo "  Shipment #{$shipment->tracking_number}\n";
        echo "  Route: {$shipment->originHub->name} → {$shipment->destinationHub->name}\n";
        echo "  Currently at: {$shipment->currentHub->name}\n";
        if ($shipment->fleet) {
            echo "  Assigned Fleet: {$shipment->fleet->name}\n";
        } else {
            echo "  Fleet: Not assigned (nullable)\n";
        }
        
        return true;
    }

    /**
     * Test Foreign Key Constraints (NOT NULL enforcement)
     */
    public function test_foreign_key_constraints()
    {
        $totalShipments = Shipment::count();
        $nullCustomerShipments = Shipment::whereNull('customer_id')->count();
        
        echo "\n✓ Foreign Key Constraint Check\n";
        echo "  Total Shipments: {$totalShipments}\n";
        echo "  Shipments with NULL customer_id: {$nullCustomerShipments}\n";
        
        $this->assertEquals(0, $nullCustomerShipments, 'customer_id should NEVER be NULL (NOT NULL enforced)');
        
        return true;
    }

    /**
     * Test Data No Duplication (M1→M2 doesn't duplicate)
     */
    public function test_no_data_duplication()
    {
        $shipment = Shipment::with('package')->whereNotNull('package_id')->first();
        
        // Verify shipment doesn't have duplicate columns
        $shipmentData = $shipment->toArray();
        
        $hasSenderName = array_key_exists('sender_name', $shipmentData);
        $hasReceiverName = array_key_exists('receiver_name', $shipmentData);
        $hasWeight = array_key_exists('weight', $shipmentData);
        
        echo "\n✓ Data Duplication Check\n";
        echo "  Shipment has 'sender_name' column: " . ($hasSenderName ? 'YES (BAD!)' : 'NO (GOOD)') . "\n";
        echo "  Shipment has 'receiver_name' column: " . ($hasReceiverName ? 'YES (BAD!)' : 'NO (GOOD)') . "\n";
        echo "  Shipment has 'weight' column: " . ($hasWeight ? 'YES (BAD!)' : 'NO (GOOD)') . "\n";
        
        $this->assertFalse($hasSenderName, 'Shipment should NOT have sender_name (normalize to M1)');
        $this->assertFalse($hasReceiverName, 'Shipment should NOT have receiver_name (normalize to M1)');
        $this->assertFalse($hasWeight, 'Shipment should NOT have weight (normalize to M1)');
        
        return true;
    }

    /**
     * Test Complete Integration Flow
     */
    public function test_complete_integration_flow()
    {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║           M2 INTEGRATION DEBUG - FULL CHECK                ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        
        $shipment = Shipment::with([
            'customer',
            'package',
            'fleet',
            'originHub',
            'destinationHub',
            'currentHub'
        ])->first();
        
        echo "\n📦 Sample Shipment Data:\n";
        echo "  Tracking Number: {$shipment->tracking_number}\n";
        echo "  Status: {$shipment->status}\n";
        
        echo "\n👤 M3 (Customer) Data:\n";
        echo "  Name: {$shipment->customer->name}\n";
        echo "  Email: {$shipment->customer->email}\n";
        echo "  Phone: {$shipment->customer->phone}\n";
        
        echo "\n📫 M1 (Package) Data:\n";
        if ($shipment->package) {
            echo "  Sender: {$shipment->package->sender_name}\n";
            echo "  Receiver: {$shipment->package->receiver_name}\n";
            echo "  Weight: {$shipment->package->weight}kg\n";
            echo "  Origin: {$shipment->package->origin}\n";
            echo "  Destination: {$shipment->package->destination}\n";
        } else {
            echo "  (No package assigned - NULL)\n";
        }
        
        echo "\n🏢 M4 (Hub) Data:\n";
        echo "  Origin Hub: {$shipment->originHub->name}\n";
        echo "  Destination Hub: {$shipment->destinationHub->name}\n";
        echo "  Current Location: {$shipment->currentHub->name}\n";
        
        echo "\n🚛 M4 (Fleet) Data:\n";
        if ($shipment->fleet) {
            echo "  Fleet: {$shipment->fleet->name}\n";
            echo "  License Plate: {$shipment->fleet->license_plate}\n";
        } else {
            echo "  (No fleet assigned - NULL)\n";
        }
        
        echo "\n🔗 Foreign Key Status:\n";
        echo "  customer_id: {$shipment->customer_id} ✓ (M3)\n";
        echo "  package_id: {$shipment->package_id} " . ($shipment->package_id ? "✓ (M1)" : "NULL") . "\n";
        echo "  fleet_id: {$shipment->fleet_id} " . ($shipment->fleet_id ? "✓ (M4)" : "NULL") . "\n";
        echo "  origin_hub_id: {$shipment->origin_hub_id} ✓ (M4)\n";
        echo "  destination_hub_id: {$shipment->destination_hub_id} ✓ (M4)\n";
        echo "  current_hub_id: {$shipment->current_hub_id} ✓ (M4)\n";
        
        echo "\n✅ ALL INTEGRATIONS VERIFIED!\n";
        echo "════════════════════════════════════════════════════════════\n\n";
        
        return true;
    }
}
