<?php

use App\Exports\GenericExport;
use App\Imports\GenericImport;
use App\Models\AIAssist;
use App\Models\Datalet;
use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Permission;
use App\Models\Relationship;
use App\Models\RelationshipModule;
use App\Models\Search;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/run-command/{command}', function (Request $request, $command) {

    $allowedCommands = [
        'cache-clear' => 'cache:clear',
        'iceburg-create' => 'iceburg:create',
        'iceburg-populate' => 'iceburg:populate',
    ];

    if (!array_key_exists($command, $allowedCommands)) {
        return response()->json(['error' => 'Command not allowed'], 403);
    }

    // Convert all query parameters to command options
    $parameters = [];
    foreach ($request->query() as $key => $value) {
        // Assumes all parameters are options with '--'
        $parameters["--$key"] = $value;
    }

    // Execute the Artisan command
    $exitCode = Artisan::call($allowedCommands[$command], $parameters);

    return response()->json([
        'message' => "Command executed with exit code: {$exitCode}"
    ]);
});
