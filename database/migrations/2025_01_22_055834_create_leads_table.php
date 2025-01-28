<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('property_type');
            $table->string('location');
            $table->bigInteger('budget');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->enum('status', ['new', 'contacted', 'scheduled', 'closed'])->default('new');
            $table->enum('source', ['website', 'referral', 'social media'])->default('website');

      
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

       
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
