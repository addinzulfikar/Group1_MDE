<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::statement("ALTER TABLE warehouses MODIFY COLUMN status ENUM('active','inactive','available','full','overload') DEFAULT 'active'");
DB::table('warehouses')->whereIn('status', ['available', 'full', 'overload'])->update(['status' => 'active']);
echo "Done.";
