<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SupportMessagesMakeUserIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->integer('reply_to')->unsigned()->nullable();
            $table->dropColumn('status');
            if ($this->tableHasForeign('support_messages', 'support_dialog_id')) {
                $table->dropForeign(['support_dialog_id']);
            }
            $table->dropColumn('support_dialog_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->change();
            $table->string('status')->nullable();
            $table->integer('support_dialog_id')->unsigned();
            $table->dropColumn('reply_to');
        });
    }

    public function tableHasForeign(string $table, string $column): bool
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        $foreigns = array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));

        foreach ($foreigns as $foreign) {
            if (Str::contains($foreign, $column)) {
                return true;
            }
        }

        return false;
    }
}
