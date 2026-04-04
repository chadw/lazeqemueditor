<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tradeskill_container_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('skill')->nullable();
            $table->timestamps();
        });

        Schema::create('tradeskill_container_template_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tradeskill_container_template_id');
            $table->foreign('tradeskill_container_template_id', 'ts_cti_template_fk')
                ->references('id')
                ->on('tradeskill_container_templates')
                ->cascadeOnDelete();

            $table->unsignedInteger('item_id');
            $table->timestamps();

            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tradeskill_container_template_items');
        Schema::dropIfExists('tradeskill_container_templates');
    }
};
