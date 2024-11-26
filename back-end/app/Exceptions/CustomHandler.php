<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class CustomHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return response()->json(['error' => 'Problème de connexion à la base de données. Veuillez vérifier le serveur de base de données.'], 500);
        }

        return parent::render($request, $exception);
    }
}
