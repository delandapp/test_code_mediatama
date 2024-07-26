<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoMaterialsAndRequestsTable extends Migration
{
    public function up()
    {
        // Tabel untuk menyimpan materi video
        Schema::create('video_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('video_url');
            $table->string('thumbnail_url');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('video_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_material_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->string('expires_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('video_material_id')->references('id')->on('video_materials')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_requests');
        Schema::dropIfExists('video_materials');
    }
}
