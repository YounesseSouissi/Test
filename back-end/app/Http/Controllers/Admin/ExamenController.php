<?php

namespace App\Http\Controllers\Admin;

use App\Models\Examen;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Examen::where('user_id', null)->get();
        return response()->json([
            'examen' => $questions,
        ]);
    }
    public function generate(Request $request)
    {

        $credentials=$request->validate([
            'module'=>'required',
            'chapitres'=>'required',
            'facile'=>'required',
            'moyen'=>'required',
            'difficile'=>'required',
        ]);
        $chapitres =$credentials['chapitres'];
        $questionsFaciles = Question::whereIn('chapitre_id', $chapitres)
            ->where('confirme', 1)
            ->where('difficulty', 'Facile')
            ->with('reponses')
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', true); // Au moins une réponse correcte
            })
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', false); // Au moins une réponse incorrecte
            })
            ->inRandomOrder()
            ->limit($credentials['facile'])
            ->get();

        $questionsMoyens = Question::whereIn('chapitre_id', $chapitres)
            ->where('confirme', 1)
            ->where('difficulty', 'Moyen')
            ->with('reponses')
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', true); // Au moins une réponse correcte
            })
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', false); // Au moins une réponse incorrecte
            })
            ->inRandomOrder()
            ->limit($credentials['moyen'])
            ->get();

        $questionsDifficiles = Question::whereIn('chapitre_id', $chapitres)
            ->where('confirme', 1)
            ->where('difficulty', 'Difficile')
            ->with('reponses')
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', true); // Au moins une réponse correcte
            })
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', false); // Au moins une réponse incorrecte
            })
            ->inRandomOrder()
            ->limit($credentials['difficile'])
            ->get();

        // Fusionner and melange les résultats
        $questions = $questionsFaciles->concat($questionsMoyens)->concat($questionsDifficiles)->shuffle();
        $errors = [];
        if ($questionsFaciles->count() != $request->facile) {
            $errors['facile'] = "The chosen number of easy questions is not available";
        }
        if ($questionsMoyens->count() != $request->moyen) {
            $errors['moyen'] = "The chosen number of medium questions is not available";
        }
        if ($questionsDifficiles->count() != $request->difficile) {
            $errors['difficile'] = "The chosen number of difficult questions is not available";
        }
        if (!empty($errors)) {
            return  response()->json(['errors' => $errors], 403);
        } else {
            $this->store($questions);
            return $this->index();
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store($questions)
    {
        $this->deleteExam();
        foreach ($questions as $question) {
            $examen = new Examen();
            $examen->question_id = $question->id;
            $examen->question_text = $question->question_text;
            $examen->difficulty = $question->difficulty;
            $examen->chapitre_id = $question->chapitre_id;
            $examen->reponses = $question->reponses;
            $examen->save();
        }
    }
    public function updateExam(Request $request)
    {
        $chapitres = $request->chapitres;
        $newQuestions = Question::whereIn('chapitre_id', $chapitres)
            ->whereNotIn('id', $request->idsExist)
            ->where('confirme', 1)
            ->with('reponses')
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', true); // Au moins une réponse correcte
            })
            ->whereHas('reponses', function ($query) {
                $query->where('correcte', false); // Au moins une réponse incorrecte
            })
            ->inRandomOrder()
            ->limit(count($request->ids))
            ->get();
        if ($newQuestions->count() !== count($request->ids)) {
            return response()->json([
                'error' => "The number of available questions does not match the requested number",
            ],403);
        } else {
            foreach ($request->ids as $id) {
                // Trouver la question existante
                $question = Examen::where('id', $id)->first();

                // Trouver une nouvelle question aléatoire
                $newQuestion = $newQuestions->shift();

                // Mettre à jour les informations de la question existante avec les informations de la nouvelle question
                $question->question_id = $newQuestion->id;
                $question->question_text = $newQuestion->question_text;
                $question->difficulty = $newQuestion->difficulty;
                $question->chapitre_id = $newQuestion->chapitre_id;
                $question->reponses = $newQuestion->reponses;
                $question->update();
            }
        }
    }
    public function deleteExam()
    {
        Examen::where('user_id', null)->delete();

    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
