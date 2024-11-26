<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Reponse;
use App\Models\Chapitre;
use App\Models\Question;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\QuestionDifficulty;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search= $request->search;
        $idModule= $request->module;
        $chapitres= json_decode($request->chapitres);

        $allQuestions = Question::where('confirme', 1);

        if ($idModule && !$search && !$chapitres) {
            $module = Module::findOrFail($idModule);
            $moduleChapitres = $module->chapitres()->pluck('id');
            $questions = $allQuestions->whereIn('chapitre_id', $moduleChapitres);
        } elseif ($idModule && $search && !$chapitres) {
            $module = Module::findOrFail($idModule);
            $moduleChapitres = $module->chapitres()->pluck('id');
            $questions = $allQuestions->whereIn('chapitre_id', $moduleChapitres)
                ->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('question_text', 'like', "%{$search}%");
                });
        } elseif ($idModule && $chapitres && !$search) {
            $questions = $allQuestions->whereIn('chapitre_id', $chapitres);
        } elseif ($idModule && $chapitres && $search) {
            $questions = $allQuestions->whereIn('chapitre_id', $chapitres)
                ->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('question_text', 'like', "%{$search}%");
                });
        } elseif ($search && !$idModule && !$chapitres) {
            $questions = $allQuestions->where(function ($query) use ($search ){
                $query->where('id', $search)
                    ->orWhere('question_text', 'like', "%{$search}%");
            });
        } else {
            $questions = $allQuestions;
        }

        $questionss = $questions->with('reponses')->get();

        return response()->json(['questions' => $questionss]);
    }

    public function getQuestionsNonConfirme(Request $request)
    {
        $search = $request->search;
        if (!$search) {
            $questions = Question::where('confirme', 0)
                ->with(['user', 'reponses'])
                ->get()
                ->map(function ($question) {
                    $question->user_name = optional($question->user)->firstname . " " . optional($question->user)->lastname;
                    unset($question->user);
                    return $question;
                });
        } else {
            $questions =  Question::where('confirme', 0)->where('id', $search)
                ->orwhere('question_text', 'like', "%{$search}%")
                ->get()
                ->map(function ($question) {
                    $question->user_name = optional($question->user)->name;
                    unset($question->user);
                    return $question;
                });
        }
        return response()->json([
            'questions' => $questions
        ]);
    }
    public function ConfirmeQuestion(Question $question)
    {
        $question->confirme = 1;
        $question->update();
        return response()->json([
            "message" => "Question confirmed successfully"
        ]);
    }
    public function NumberQuestionsNonConfirme()
    {
        $number_questions = Question::where('confirme', 0)->count();

        return response()->json([
            'number_questions' => $number_questions
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        return response()->json([
            'data' => $question->load('reponses')
        ]);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $credentials = $request->validate([
                'question_text' => ['required', 'string', 'min:8', Rule::unique('questions')],
                'difficulty' => [new Enum(QuestionDifficulty::class)],
                'chapitre_id' => ['required', Rule::exists('chapitres', 'id')],
                'type' => 'required',
                'reponses' => 'required|array|min:1', // Au moins une réponse doit être fournie
            ], [
                'reponses.required' => 'At least one answer must be provided',
            ]);
            if ($request->type === 'single') {
                $correctAnswersCount = collect($request->reponses)->filter(function ($answer) {
                    return $answer['correcte'] ?? false;
                })->count();

                if ($correctAnswersCount !== 1) {
                    $error['errors']['reponses'] = ['One response must be marked as correct '];
                    return response()->json($error, 422);
                }
            }
            $credentials['confirme'] = 1;
            $question = Question::create($credentials);
            $answers = $request->reponses;
            foreach ($answers as $answer) {
                $reponse = [
                    'reponse_text' => $answer['reponse_text'],
                    'correcte' => $answer['correcte'],
                    'question_id' => $question->id
                ];
                $validator = Validator::make($reponse, [
                    'reponse_text' => 'required',
                    'correcte' => 'required|bool',
                    'question_id' => ['required', Rule::exists('questions', 'id')],
                ], ['correcte.required' => 'Le champ reponse est obligatoire']);
                Reponse::create($validator->validate());
            }
            DB::commit();
            return response()->json(
                [
                    'question' => Question::with('reponses')->find($question->id),
                    'message' => 'question created successfully'
                ]
            );
        } catch (\PDOException $e) {
            report($e);
            DB::rollBack();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        DB::beginTransaction();
        try {
            $answers = $request->reponses;
            $credentials = $request->validate([
                'question_text' => ['required', 'string', 'min:8', Rule::unique('questions')->ignore($question)],
                'difficulty' => [new Enum(QuestionDifficulty::class)],
                'chapitre_id' => ['required', Rule::exists('chapitres', 'id')],
                'type' => 'required',
                'reponses' => 'required|array|min:1', // Au moins une réponse doit être fournie
            ], [
                'reponses.required' => 'At least one answer must be provided',
            ]);
            if ($request->type === 'single') {
                $correctAnswersCount = collect($request->reponses)->filter(function ($answer) {
                    return $answer['correcte'] ?? false;
                })->count();

                if ($correctAnswersCount !== 1) {
                    $error['errors']['reponses'] = ['One response must be marked as correct '];
                    return response()->json($error, 422);
                }
            }
            $question->update($credentials);
            // Get ids as plain array of existing answers
            $existsIds = $question->reponses->pluck('id')->toArray();
            // Get ids as plain array of new  answers
            $newIds = Arr::pluck($answers, 'id');
            // Find answers to delete
            $toDelete = array_diff($existsIds, $newIds);
            // Find answers to add
            $toAdd = array_diff($newIds, $existsIds);
            Reponse::destroy($toDelete);
            foreach ($answers as $answer) {
                if (in_array($answer['id'], $toAdd)) {
                    $reponse = [
                        'reponse_text' => $answer['reponse_text'],
                        'correcte' => $answer['correcte'],
                        'question_id' => $question->id
                    ];
                    $validator = Validator::make($reponse, [
                        'reponse_text' => 'required',
                        'correcte' => 'required|bool',
                        'question_id' => ['required', Rule::exists('questions', 'id')],
                    ], ['correcte.required' => 'Le champ reponse est obligatoire']);
                    Reponse::create($validator->validate());
                }
                // Updating exists answers
                $ReponsesExists = collect($answers)->keyBy('id');
                foreach ($question->reponses as $reponse) {
                    if (isset($ReponsesExists[$reponse->id])) {
                        $credentials = $ReponsesExists[$reponse->id];
                        $credentials['question_id'] = $question->id;
                        $validator = Validator::make($credentials, [
                            'reponse_text' => 'required',
                            'correcte' => 'required|bool',
                            'question_id' => ['required', Rule::exists('questions', 'id')],
                        ]);
                        $reponse->update($validator->validate());
                    }
                }
            }
            DB::commit();
            return response()->json(
                [
                    'question' => Question::with('reponses')->find($question->id),
                    'message' => 'question updated successfully'
                ]
            );
        } catch (\PDOException $e) {
            report($e);
            DB::rollBack();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json([
            'message' => 'question deleted successfully'
        ]);
    }
}
