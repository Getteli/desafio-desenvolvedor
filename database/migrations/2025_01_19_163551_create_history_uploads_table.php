<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('upload_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamp('uploaded_at')->nullable();
            $table->string('uploaded_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('upload_histories');
    }
};
