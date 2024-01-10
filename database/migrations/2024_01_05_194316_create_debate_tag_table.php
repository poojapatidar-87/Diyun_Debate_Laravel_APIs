<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebateTagTable extends Migration
{
    public function up(): void
    {
        Schema::create('debate_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debate_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            // Add foreign keys
            $table->foreign('debate_id')->references('id')->on('debate')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('debate_tag');
    }
}
