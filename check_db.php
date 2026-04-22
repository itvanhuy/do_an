<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$searchFile = '1774935609.png';
$tables = DB::select('SHOW TABLES');
foreach($tables as $table) {
    $tableName = current((array)$table);
    $columns = Schema::getColumnListing($tableName);
    foreach(['image', 'logo', 'team1_logo', 'team2_logo', 'team_logo'] as $col) {
        if (in_array($col, $columns)) {
            $count = DB::table($tableName)->where($col, $searchFile)->count();
            if ($count > 0) {
                echo "Found '$searchFile' in table '$tableName' column '$col'\n";
            }
        }
    }
}
echo "Search completed.\n";
