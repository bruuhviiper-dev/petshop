<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petshop_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('weekday')->comment('0=dom, 1=seg, 2=ter, 3=qua, 4=qui, 5=sex, 6=sab');
            $table->time('open')->nullable();
            $table->time('close')->nullable();
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
