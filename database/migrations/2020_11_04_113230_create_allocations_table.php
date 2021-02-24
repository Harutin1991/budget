<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->integer('id',true,true);
            $table->integer('allocation_type_id')->index()->unsigned();
            $table->integer('sea_id')->index()->unsigned();
            $table->integer('lea_id')->index()->unsigned();
            $table->integer('ses_id')->index()->unsigned();
            $table->float('total_instruction')->default(0);
            $table->float('total_allocation')->default(0);
            $table->float('family_engagement')->default(0);
            $table->float('professional_development')->default(0);
            $table->float('professional_development_percentage')->default(0);
            $table->float('materials')->default(0);
            $table->float('total_allocation_amount')->default(0);
            $table->float('well_rounded_percentage')->default(0);
            $table->float('well_rounded_amount')->default(0);
            $table->float('safe_healthy_percentage')->default(0);
            $table->float('safe_healthy_amount')->default(0);
            $table->float('teach_instruction_percentage')->default(0);
            $table->float('teach_instruction_amount')->default(0);
            $table->float('teach_professional_development_amount')->default(0);
            $table->tinyInteger('is_final')->default(0);
            $table->date('creation_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocations');
    }
}
