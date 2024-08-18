<?php

use App\Models\HolidayPlan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on((new User())->getTable())->onDelete('cascade');
            $table->bigInteger('holiday_plan_id')->unsigned();
            $table->foreign('holiday_plan_id')->references('id')->on((new HolidayPlan())->getTable())->onDelete('cascade');
            $table->timestamps();
            $table->primary(['user_id', 'holiday_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
