<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Changing ENUM to VARCHAR(50) to allow 'sanyojak' and future roles without truncation
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'staff'");
    }

    public function down()
    {
        // To rollback, we revert to the known ENUMs
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff') DEFAULT 'staff'");
    }
};
