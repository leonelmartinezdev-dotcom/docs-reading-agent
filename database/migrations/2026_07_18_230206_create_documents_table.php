<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Image\Transformations\Blur;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('document_types', function (Blueprint $table) {
            $table->id('id');
            $table->string('label');
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->longText('prompt')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->nullable();
            $table->string('url', 200);
            $table->string('extension', 100);
            $table->string('size', 200);
            $table->string('original_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('document_type_id')->constrained('document_types')->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->nullOnDelete();
            $table->boolean('requires_analysis')->default(0);
            $table->enum('status', [
                'pending',
                'processing',
                'approved',
                'rejected',
                'skipped',
            ])->default('approved');
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('status_changed_at')->nullable();
            $table->morphs('documentable');
            $table->timestamps();
        });


        Schema::create('document_analyses', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->boolean('approved')->default(false);
            $table->string('description', 200)->nullable();
            $table->float('confidence', 2);
            $table->enum('status', [
                'completed',
                'error',
            ])->default('completed');
            $table->longText('content')->nullable();
            $table->text('errors')->nullable();
            $table->text('warnings')->nullable();
            $table->jsonb('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
