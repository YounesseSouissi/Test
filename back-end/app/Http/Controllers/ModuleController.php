<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Chapitre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $module=$chapitre->module;
        return response()->json([
            'module' => $module
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
}
