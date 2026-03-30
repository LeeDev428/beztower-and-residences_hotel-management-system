<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method VARCHAR(100) NOT NULL DEFAULT 'gcash'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method TYPE VARCHAR(100)");
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method SET DEFAULT 'gcash'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash','bank_transfer','gcash','paymaya') NOT NULL DEFAULT 'gcash'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("UPDATE payments SET payment_method = 'gcash' WHERE payment_method NOT IN ('cash','bank_transfer','gcash','paymaya')");
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method TYPE VARCHAR(20)");
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IN ('cash','bank_transfer','gcash','paymaya'))");
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method SET DEFAULT 'gcash'");
        }
    }
};
