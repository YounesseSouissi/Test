<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Chapitre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\search;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        if (!$search) {
            $modules = Module::all();
        } else {
            $modules = Module::where('id', $search)
                ->orwhere('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->get();
        }
        return response()->json(['modules' => $modules]);
    }

    public function getModuleByChapitre(Request $request)
    {
        $chapitre = Chapitre::findOrFail($request->id);
        $module = $chapitre->module;
        return response()->json([
            'module' => $module
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' =>['required','string','min:8', Rule::unique('modules')],
            'description' => 'required|string'
        ]);
        $module = Module::create($credentials);
        return response()->json([
            'module' => $module,
            'message' => 'module created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return response()->json([
            'data' => $module
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $credentials = $request->validate([
            'name' =>['required','string','min:8', Rule::unique('modules')->ignore($module)],
            'description' => 'required|string'
        ]);
        $module->update($credentials);
        return response()->json([
            'module' => $module,
            'message' => 'module updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json([
            'message' => 'module deleted successfully'
        ]);
    }
}
