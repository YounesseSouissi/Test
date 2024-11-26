<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapitre;
use App\Models\Module;
use App\Models\Question;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::count();
        $modules = Module::count();
        $chapitres = Chapitre::count();
        $questions = Question::count();
        $questionsFacile = Question::where('difficulty','Facile')->count();
        $questionsMoyen = Question::where('difficulty','Moyen')->count();
        $questionsDifficile = Question::where('difficulty','Difficile')->count();
        $reponses = Reponse::count();
        return response()->json([
            'counts' => [
                'users' => $users,
                'modules' => $modules,
                'chapitres' => $chapitres,
                'questions' => $questions,
                'reponses' => $reponses,
                'facile' => $questionsFacile,
                'moyen' => $questionsMoyen,
                'difficile' => $questionsDifficile,
            ]
        ]);

    }
}
