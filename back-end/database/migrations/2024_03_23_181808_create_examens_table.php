<?php

use App\Models\User;
use App\Models\Chapitre;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('question_id');
            $table->text('question_text');
            $table->enum('difficulty', ['Facile', 'Moyen', 'Difficile']);
            $table->foreignIdFor(User::class,'user_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Chapitre::class,'chapitre_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->text('reponses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};
