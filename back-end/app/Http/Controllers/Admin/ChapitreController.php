<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chapitre;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ChapitreController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        if (!$search) {
            $chapitres = Chapitre::all();
        } else {
            $chapitres = Chapitre::where('id', $search)
                ->orwhere('title', 'like', "%{$search}%")
                ->get();
        }
        return response()->json(['chapitres' => $chapitres]);
    }
    public function getChapitresByModule(Request $request)
    {
        $chapitres = Chapitre::where('module_id',$request->id)
        ->get();
        return response()->json([
            'chapitres' => $chapitres
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'title' =>['required','string','min:8', Rule::unique('chapitres')],
            'module_id' => ['required', Rule::exists('modules', 'id')]
        ]);
        $chapitre = Chapitre::create($credentials);
        return response()->json([
            'chapitre' => $chapitre,
            'message' => 'chapitre created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chapitre $chapitre)
    {
        return response()->json([
            'data' => $chapitre
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chapitre $chapitre)
    {
        $credentials = $request->validate([
            'title' =>['required','string','min:8', Rule::unique('chapitres')->ignore($chapitre)],
            'module_id' => ['required', Rule::exists('modules', 'id')]
        ]);
        $chapitre->update($credentials);
        return response()->json([
            'chapitre' => $chapitre,
            'message' => 'chapitre updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chapitre $chapitre)
    {
        $chapitre->delete();
        return response()->json([
            'message' => 'chapitre deleted successfully'
        ]);
    }
}
