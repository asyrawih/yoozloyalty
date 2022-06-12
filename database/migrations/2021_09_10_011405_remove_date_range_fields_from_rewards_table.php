<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDateRangeFieldsFromRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            if(Schema::hasColumn('rewards', 'active_from')) { $table->dropColumn('active_from'); }
            if(Schema::hasColumn('rewards', 'expires_at')) { $table->dropColumn('expires_at'); }
            if(Schema::hasColumn('rewards', 'active_monday')) { $table->dropColumn('active_monday'); }
            if(Schema::hasColumn('rewards', 'active_tuesday')) { $table->dropColumn('active_tuesday'); }
            if(Schema::hasColumn('rewards', 'active_wednesday')) { $table->dropColumn('active_wednesday'); }
            if(Schema::hasColumn('rewards', 'active_thursday')) { $table->dropColumn('active_thursday'); }
            if(Schema::hasColumn('rewards', 'active_friday')) { $table->dropColumn('active_friday'); }
            if(Schema::hasColumn('rewards', 'active_saturday')) { $table->dropColumn('active_saturday'); }
            if(Schema::hasColumn('rewards', 'active_sunday')) { $table->dropColumn('active_sunday'); }
            if(Schema::hasColumn('rewards', 'active_monday_from')) { $table->dropColumn('active_monday_from'); }
            if(Schema::hasColumn('rewards', 'active_tuesday_from')) { $table->dropColumn('active_tuesday_from'); }
            if(Schema::hasColumn('rewards', 'active_wednesday_from')) { $table->dropColumn('active_wednesday_from'); }
            if(Schema::hasColumn('rewards', 'active_thursday_from')) { $table->dropColumn('active_thursday_from'); }
            if(Schema::hasColumn('rewards', 'active_friday_from')) { $table->dropColumn('active_friday_from'); }
            if(Schema::hasColumn('rewards', 'active_saturday_from')) { $table->dropColumn('active_saturday_from'); }
            if(Schema::hasColumn('rewards', 'active_sunday_from')) { $table->dropColumn('active_sunday_from'); }
            if(Schema::hasColumn('rewards', 'active_monday_to')) { $table->dropColumn('active_monday_to'); }
            if(Schema::hasColumn('rewards', 'active_tuesday_to')) { $table->dropColumn('active_tuesday_to'); }
            if(Schema::hasColumn('rewards', 'active_wednesday_to')) { $table->dropColumn('active_wednesday_to'); }
            if(Schema::hasColumn('rewards', 'active_thursday_to')) { $table->dropColumn('active_thursday_to'); }
            if(Schema::hasColumn('rewards', 'active_friday_to')) { $table->dropColumn('active_friday_to'); }
            if(Schema::hasColumn('rewards', 'active_saturday_to')) { $table->dropColumn('active_saturday_to'); }
            if(Schema::hasColumn('rewards', 'active_sunday_to')) { $table->dropColumn('active_sunday_to'); }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            //
        });
    }
}
