<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::table('support_messages')
            ->whereNull('user_id')
            ->update(['user_id' => 0]);

        Schema::table('support_messages', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->change();
            $table->string('status')->nullable();
            $table->integer('support_dialog_id')->unsigned();
            $table->dropColumn('reply_to');
        });
    }

    public function tableHasForeign(string $table, string $column): bool
    {
        $connection = Schema::getConnection();

        if (method_exists($connection, 'getDoctrineSchemaManager')) {
            $foreigns = array_map(function ($key) {
                return $key->getName();
            }, $connection->getDoctrineSchemaManager()->listTableForeignKeys($table));

            foreach ($foreigns as $foreign) {
                if (Str::contains($foreign, $column)) {
                    return true;
                }
            }

            return false;
        }

        $schema = $connection->getSchemaBuilder();

        if (method_exists($schema, 'getForeignKeys')) {
            foreach ($schema->getForeignKeys($table) as $foreign) {
                $name = $foreign['name'] ?? null;
                $columns = $foreign['columns'] ?? [];

                if (is_string($name) && Str::contains($name, $column)) {
                    return true;
                }

                if (is_array($columns) && in_array($column, $columns, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
