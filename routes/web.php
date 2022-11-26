<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Module;
use App\Models\Field;
use App\Models\Relationship;
use App\Models\ModuleSubpanel;
use App\Models\Search;
use App\Models\RelationshipModule;
use Illuminate\Support\Facades\DB;
use App\Models\UserSetting;
use App\Models\Logs;
use App\Models\Datalet;
use App\Exports\GenericExport;
use App\Imports\GenericImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Collection;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin/data/module', function ($request) {
    if(Auth::user()->role != 'Admin')
        return response()->json(['error' => 'No Access'], 422);

    return response()->json(Admin::getData($request));
})->middleware(['auth', 'verified'])
    ->name('relationship_fields');

Route::post('/data/delete/{base_id}/type/{type}', function (Request $request, $baseId, $type='module') {
    $moduleId=$baseId;
    if($type == "relationship"){
       $moduleId=RelationshipModule::where('relationship_id', $baseId)->value('module_id');
    }
    if(!Permission::checkPermission($moduleId, 'write'))
        return response()->json(['error' => 'No Access'], 422);

    $status=false;
    switch($type)
    {
        case 'module':
            $status=Module::deleteRecords($baseId, $request->toArray());
            break;
        case 'relationship':
            $status=Relationship::deleteRecords($baseId, $request->toArray());
            break;
        default:
            break;
    }

    return response()->json(
        ['success' => $status]
    );
})->middleware(['auth', 'verified'])
    ->name('get_record');

Route::get('/data/module/{module_id}/record/{record_id}', function ($moduleId=0, $recordId=0) {
    if(!Permission::checkPermission($moduleId))
        return response()->json(['error' => 'No Access'], 422);

    return response()->json(
        Collect(Module::getRecord($moduleId, $recordId))->slice(0, -6)->toArray()
    );
})->middleware(['auth', 'verified'])
    ->name('get_record');

Route::get('/data/search_fields/{value}/search_type/{type}', function ($moduleID = '', $type = 'relationship') {

    $module=Module::where('id', $moduleID)->firstOrFail();
    $fields=Search::getFields($module->id, $type);
    return response()->json($fields);

})->middleware(['auth', 'verified'])
    ->name('relationship_fields');



Route::any('/data/download/{module_id}/{type}', function (Request $request, $moduleId=1, $type='xlsx') {
    if(!Permission::checkPermission($moduleId, 'export'))
        return response()->json(['error' => 'No Access'], 422);
    $data=new Collection(
        Module::getRecords($moduleId, $request->all(), true)->toArray()
    );
    return Excel::download(new GenericExport($data), 'users.' . $type);
})->middleware(['auth', 'verified'])
    ->name('record_save');

Route::any('/data/permissions', function () {
    if(Auth::user()->role != 'Admin')
        return response()->json(['error' => 'No Access'], 422);
    return response()->json(
        Permission::with('modules')->with('roles')->get()
    );
})->middleware(['auth', 'verified'])
    ->name('permissions_data');

Route::any('/data/permissions/save', function (Request $request) {
    if(Auth::user()->role != 'Admin')
        return response()->json(['error' => 'No Access'], 422);
    return response()->json(
        Permission::where('id', $request->input('id',0))->
        update([
            'can_' . $request->input('type', 'read') => $request->current_state === 0 ? 1 : 0
        ])
    );
})->middleware(['auth', 'verified'])
    ->name('permission_save');

Route::any('/data/save', function (Request $request) {
    $data=$request->all();
    if(!Permission::checkPermission($data['module_id'], 'write'))
        return response()->json(['error' => 'No Access'], 422);

    return response()->json(Module::saveRecord($data['module_id'], $data));
})->middleware(['auth', 'verified'])
    ->name('module_record_save');

Route::any('/data/subpanel/save', function (Request $request) {
    [$subpanelId, $selectedRecords, $newRecords, $recordId] = ModuleSubpanel::parseRequest($request);
    $subpanel=ModuleSubpanel::find($subpanelId);
    if(!Permission::checkPermission($subpanel->module_id, 'write'))
        return response()->json(['error' => 'No Access'], 422);

    if($subpanelId)
    {
        return response()->json(
            ModuleSubpanel::processRecords($subpanelId, $selectedRecords, $newRecords, $recordId)
        );
    }

    return response()->json(['error' => 'Missing Subpanel ID'], 500);

})->middleware(['auth', 'verified'])
    ->name('module_record_save');


Route::any('/data/import', function (Request $request) {

    $temp_file=request()->file('input_file')->store('temp');
    $fields=[];

    $firstRowHeader=0;
    if($request->first_row_header === "true")
    {
        $firstRowHeader=1;
    }
    $import = new GenericImport();
    Excel::import($import, $temp_file);
    $data=$import->data;

    if($request->input('module_id'))
    {
        $module=Module::where('id', $request->input('module_id'))->firstOrFail();
        if(!Permission::checkPermission($module->id, 'import'))
            return response()->json(['error' => 'No Access'], 422);

        $fields=Field::where('module_id', $module->id)
            ->where('status', 1)
            ->get()
            ->pluck('name')
            ->toArray();
    }
    else {
        foreach($import->data[0] as $item)
        {
            $fields[] = $item;
        }
    }

    if($request->input('preview', 0) == true)
    {

        return response()->json([
            'preview' => 1,
          'fields' => $fields,
          'row' => $import->data[0][$firstRowHeader]
        ]
        );
    }
    $data=Module::replaceValuesForRelatedIds($module->id, $data[0], $firstRowHeader);
    $results=Module::insertImport($module, $data);

    return response()->json([
        'records_updated' => count($data),
        ]
    );
})->middleware(['auth', 'verified'])
    ->name('module_record_import');

Route::get('/data/datalet', function (Request $request) {
    return response()->json(Datalet::getData($request->id));
})->middleware(['auth', 'verified'])->name('data')
    ->name('dashlet');

Route::get('/data/search_data', function (Request $request) {
    return response()->json(Search::getData($request->all())->toArray());
})->middleware(['auth', 'verified'])->name('search_data');

Route::post('/data/settings', function (Request $request) {

    if(Auth::user()->role != 'Admin')
        return response()->json(['error' => 'No Access'], 422);

    return response()->json(Setting::saveSettings($request->all()));
})->middleware(['auth', 'verified'])->name('settings_save');

Route::get('/data/subpanel/{subpanel_id}', function (Request $request, $id=0) {
    $subpanel = ModuleSubpanel::getSubpanelData($id, $request->all());
    return response()->json($subpanel);
})->middleware(['auth', 'verified'])->name('data')
    ->name('subpanel_relationship_fields');



Route::get('/data/related_fields/field_id/{field_id}', function ($fieldId = 0) {

    $field=Field::find($fieldId);
    $relatedModule=Module::find($field->related_module_id);
    $records=DB::table($relatedModule->name)->select($field->related_field_id, $field->related_value_id)->get();
    if(!$records)
    {
        $records='Unknown';
    }
    return response()->json($records->toArray());

})->middleware(['auth', 'verified'])->name('data')
    ->name('data_related');

Route::get('/data/related_field_name/field_id/{field_id}/value/{value}', function (Request $request, $fieldId = 0, $value = 0) {
    $field=Field::find($fieldId);
    $relatedModule=Module::find($field->related_module_id);

    $record=DB::table($relatedModule->name)->where($field->related_field_id, $value)->value($field->related_value_id);
    if(!$record)
    {
        $record='Unknown';
    }
    return response()->json($record);

})->middleware(['auth', 'verified'])->name('data')
    ->name('data_related');


Route::get('/audit_log/{moduleId}', function ($moduleId= '', $id = 0) {

    if(!Permission::checkPermission($moduleId, 'read'))
        return redirect('dashboard')->withErrors(['No Access']);

    $module = Module::where('id', $moduleId)->first();

    return Inertia::render('Module/AuditLog', [
        'logs' => Logs::where('module_id', $moduleId)
            ->with('module')
            ->with('user')
            ->get(),
        'users' => User::all(),
        'module' => $module,
        'permissions' => Permission::getPermissions($moduleId),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search', 'url'=> '/module/' . $module->name, 'svg' => 'settings'],
            ['name' => 'Audit Log', 'url'=>'', 'svg' => 'settings']
        )
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('audit_log');


Route::get('/module/{name}/edit/{id}', function ($moduleName= '', $id = 0) {

    $module=Module::where('name', $moduleName)
        ->with('fields')
        ->first();

    if(!Permission::checkPermission($module->id, 'write'))
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Module/Add', [
        'module' => $module,
        'fields' => Field::where('module_id', $module->id)
            ->with('module')
            ->with('related_module')
            ->get(),
        'record' => Module::getRecord($module->id, $id),
        'permissions' => Permission::getPermissions($module->id),
        'relationships' => RelationshipModule::where('module_id', $module->id)
            ->with('relationship')
            ->with('modulefields')
            ->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Details', 'url'=> '/module/' . $module->name . '/view/' . $id, 'svg' => 'settings'],
            ['name' => 'Edit', 'url'=>'', 'svg' => 'settings']
        ),
        'type' => 'edit',
        'modules' => Module::all()->toArray(),
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('module_edit');

Route::get('/module/{module_name}/{action}/{record_id}', function ($moduleName = null, $action = 'view', $recordId = null) {
    $module = Module::where('name', $moduleName)->with("convertedmodules")->first();
    if(!Permission::checkPermission($module->id, $action != 'view' ? 'write' : 'read'))
        return redirect('dashboard')->withErrors(['No Access']);

    [$previous, $next] = Module::getPreviousNext($module->id, $recordId);

    return Inertia::render('Module/'.ucfirst(strtolower($action)), [
        'module' => $module->toArray(),
        'record' => Module::getRecord($module->id, $recordId),
        'next'   => $next,
        'previous' => $previous,
        'subpanel_ids' => ModuleSubpanel::where('module_id', $module->id)->get()->pluck('id'),
        'subpanels' => [],
        'permissions' => Permission::getPermissions($module->id),
        'fields' => $action != 'view' ? Search::getFields($module->id, 'module', 'Display')
         : Search::getFields($module->id, 'module', 'OrderBy'),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search', 'url'=> '/module/' . $module->name, 'svg' => 'settings'],
            ['name' => ucfirst($action), 'url'=>'', 'svg' => 'settings']
        ),
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->where('action', 'view|edit')
    ->name('module_viewedit');

Route::get('/subpanel/add/{subpanel_id}', function (Request $request, $subpanelId = 0) {
    $subpanel=ModuleSubpanel::where('id', $subpanelId)
        ->with('relationship.relationshipmodule.module.fields.module')
        ->with('module')
        ->firstOrFail();

    if(!Permission::checkPermission($subpanel->module_id, 'write'))
        return redirect('dashboard')->withErrors(['No Access']);

    $fields=[];
    $subpanel->relationship->relationshipmodule->each(function ($item, $index) use (&$fields) {

        $fields[$item->module_id]=Field::where('module_id', $item->module_id)
            ->with('module')
            ->with('related_module')
            ->get();
    });
    return Inertia::render('Subpanel/Add', [
        'subpanel' => $subpanel,
        'fields' => $fields,
        'from_module' => Module::where('id', $request->from_module)->first(),
        'from_id' => $request->from_id,
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search', 'url'=> '/module/' . $subpanel->module->name, 'svg' => 'settings'],
            ['name' => 'Add', 'url'=>'', 'svg' => 'settings'])
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('subpanel_add');

Route::get('/subpanel/{subpanel_id}/edit/{record_id}', function (Request $request, $subpanelId = 0, $recordId = 0) {

    $subpanel=ModuleSubpanel::where('id', $subpanelId)
        ->with('relationship.relationshipmodule.module.fields.module')
        ->with('module')
        ->firstOrFail();

    if(!Permission::checkPermission($subpanel->module_id, 'write'))
        return redirect('dashboard')->withErrors(['No Access']);

    $fields=[];
    $subpanel->relationship->relationshipmodule->each(function ($item, $index) use (&$fields) {
        $fields[$item->module_id]=Field::where('module_id', $item->module_id)
            ->with('module')
            ->with('related_module')
            ->get();
    });
    $record=Module::getRecord($subpanel->module_id, $recordId);

    $temp=[];
    $temp[1]=1;
    $temp[2]=2;
    return Inertia::render('Subpanel/Add', [
        'subpanel' => $subpanel,
        'fields' => $fields,
        'selected_records' => $temp,
        'record' => $record,
        'from_module' => Module::where('id', $request->from_module)->first(),
        'from_id' => $request->from_id,
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search', 'url'=> '/module/' . $subpanel->module->name, 'svg' => 'settings'],
            ['name' => 'Add', 'url'=>'', 'svg' => 'settings'])
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('subpanel_edit');


Route::get('/relationship/{name}/add', function ($relationshipName = '') {

    $relationship=Relationship::where('name', $relationshipName)->firstOrFail();
    $fields=Search::getFields($relationship->id, 'relationship');

    return Inertia::render('Module/Add', [
        'fields' => $fields,
        'record' => $record=Search::getData($relationship->id, ['search_type' => 'relationship'  ] )[0],
        'relationship' => $relationship,
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('relationship_edit');

Route::get('/relationship/{name}/add', function ($relationshipName = '') {

    $module=Module::where('name', $moduleName)
        ->with('fields')
        ->first();
    if(!Permission::checkPermission($module->id))
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Module/Add', [
        'module' => $module,
        'fields' => Field::where('module_id', $module->id)
            ->with('module')
            ->with('related_module')
            ->get(),
        'record' => null,
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search '.$module->label, 'url'=> '/module/' . $module->name, 'svg' => 'settings'],
            ['name' => 'Add', 'url'=>'', 'svg' => 'settings']),
        'relationship' => Relationship::where('name', $relationshipName)->firstOrFail(),
        'relationships' => [],
        'modules' => Module::all()->toArray(),
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('relationship_add');

Route::get('/module/{name}/add', function ($moduleName = '') {

    $module=Module::where('name', $moduleName)
        ->with('fields')
        ->first();

    if(!Permission::checkPermission($module->id, 'write'))
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Module/Add', [
        'module' => $module,
        'fields' => Field::where('module_id', $module->id)
            ->with('module')
            ->with('related_module')
            ->get(),
        'record' => null,
        'type' => 'add',
        'permissions' => Permission::getPermissions($module->id),
        'convert_from_record' => isset($_REQUEST['convert_from_record']) && isset($_REQUEST['convert_module_id'])
            ? Module::getRecord($_REQUEST['convert_module_id'], $_REQUEST['convert_from_record'])
            : null,
        'relationships' => RelationshipModule::where('module_id', $module->id)
            ->with('relationship')
            ->with('modulefields')
            ->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Search '.$module->label, 'url'=> '/module/' . $module->name, 'svg' => 'settings'],
            ['name' => 'Add', 'url'=>'', 'svg' => 'settings']),
        'modules' => Module::all()->toArray(),
    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('module_add');


Route::get('/module/{module_name}', function ($moduleName = null) {
    $module=Module::where('name', $moduleName)->with("fields")->first();
    if(!Permission::checkPermission($module->id))
        return redirect('dashboard')->withErrors(['No Access']);

    $relationships=RelationshipModule::where('module_id', $module->id)
        ->with('relationship')
        ->with('modulefields')
        ->get();

    $_GET['module_id']=$module->id;
    $_GET['search_type']='module';
    return Inertia::render('Module/List', [
        'module' => $module->toArray(),
        'modules' => Module::all()->toArray(),
        'field_data' => Field::getRelatedFieldData($module->id),
        'page' => DB::table($moduleName)->paginate(3),
        'records' => Search::getData($_GET, true)->toArray(),
        'records_object' => Search::getData($_GET),
        'display_fields' => Search::getFields($module->id, 'module', 'Display'),
        'search_fields' => Search::getFields($module->id, 'module', 'Search'),
        'order_by_fields' => Search::getFields($module->id, 'module', 'OrderBy'),
        'request' => $_GET,
        'permissions' => Permission::getPermissions($module->id),
        'relationships' => $relationships,
        'breadcrumbs' => Setting::getBreadCrumbs(['name' => 'Search', 'link'=>'', 'svg' => 'settings'])

    ]);
})->middleware(['auth', 'verified'])->name('module')
    ->name('module_list');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'datalets' => Datalet::where('role_id', 0)->orWhere('role_id', Auth::user()->role_id)
            ->with('type')
            ->with('module')
            ->with('field')
            ->with('relationship')
            ->orderBy('display_order')
            ->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/import', function (Request $request) {
    return Inertia::render('Import', [
        'modules'=> Module::where('status', 1)->get(),
        'data' => [
            'module_id' => $request->input('from_module_id') ? $request->input('from_module_id') : $request->input('module_id'),
            'module_name' => $request->input('module_name'),
            'first_row_header' => $request->input('first_row_header')
        ],
        'from_module_id' => $request->input('from_module_id'),
        'breadcrumbs' => Setting::getBreadCrumbs(['name' => 'Import', 'link'=>'', 'svg' => 'settings'])
    ]);
})->middleware(['auth', 'verified'])->name('import');

Route::get('/admin/modules', function () {
    return Inertia::render('Admin/Modules', [
        'themes' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url'=>'/admin', 'svg' => 'settings'],
            ['name' => 'Modules', 'url'=>'', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_modules');

Route::get('/admin', function () {
    return Inertia::render('Admin/Index', [
        'themes' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(['name' => 'Import', 'link'=>'', 'svg' => 'settings'])
    ]);
})->middleware(['auth', 'verified'])->name('admin');

Route::get('/admin/permissions', function () {
    if(Auth::user()->role != 'Admin')
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Admin/Permissions', [
        'permissions' => Permission::with('modules')->with('roles')->get(),
        'roles' => Role::all(),
        'modules' => Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url'=>'/admin', 'svg' => 'settings'],
            ['name' => 'Permissions', 'url'=>'', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('permissions');

Route::get('/admin/subpanels', function () {
    if(Auth::user()->role != 'Admin')
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Admin/Subpanels', [
        'subpanels' => ModuleSubpanel::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url'=>'/admin', 'svg' => 'settings'],
            ['name' => 'Subpanels', 'url'=>'', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_subpanels');

Route::get('/admin/datalets', function () {
    if(Auth::user()->role != 'Admin')
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Admin/Datalets', [
        'subpanels' => Dashlet::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(
            ['name' => 'Admin', 'url'=>'/admin', 'svg' => 'settings'],
            ['name' => 'Datalets', 'url'=>'', 'svg' => 'settings']),
    ]);
})->middleware(['auth', 'verified'])->name('admin_datalets');


Route::get('/settings', function () {
    if(Auth::user()->role != 'Admin')
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Settings', [
        'settings'=> 0,
        'themes' => Setting::getThemes(),
        'breadcrumbs' => Setting::getBreadCrumbs(['name' => 'Settings', 'link'=>'', 'svg' => 'settings'])
    ]);
})->middleware(['auth', 'verified'])->name('settings');

Route::get('/modules', function () {


    return Inertia::render('Modules', [
        'modules' => $modules=Module::where('status', 1)->get(),
        'breadcrumbs' => Setting::getBreadCrumbs(['name' => 'Modules', 'link'=>'', 'svg' => 'settings'])

    ]);
})->middleware(['auth', 'verified'])->name('rall_modules');

Route::get('/role_permission', function () {
    if(Auth::user()->role != 'Admin')
        return redirect('dashboard')->withErrors(['No Access']);

    return Inertia::render('Searches', []);
})->middleware(['auth', 'verified'])->name('role_permissions');


require __DIR__.'/auth.php';




