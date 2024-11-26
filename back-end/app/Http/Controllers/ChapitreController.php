<?php

namespace App\Http\Controllers;

use App\Models\Chapitre;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ->latest()
        ->get();
        return response()->json([
            'chapitres' => $chapitres
        ]);
    }

}
