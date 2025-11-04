<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_usd', 10, 2)->nullable()->after('price');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE currencies
                SET rate = CASE code
                    WHEN 'USD' THEN 1.000000
                    WHEN 'PLN' THEN 0.270960
                    WHEN 'EUR' THEN 1.152850
                END
                WHERE code IN ('USD', 'PLN', 'EUR')
            ");

            // Trigger on INSERT
            DB::unprepared('
                CREATE TRIGGER services_before_insert
                BEFORE INSERT ON services
                FOR EACH ROW
                BEGIN
                    DECLARE rate_value DECIMAL(15,6);
                    SELECT rate INTO rate_value FROM currencies WHERE id = NEW.currency_id LIMIT 1;
                    IF rate_value IS NOT NULL THEN
                        SET NEW.price_usd = NEW.price * rate_value;
                    ELSE
                        SET NEW.price_usd = NEW.price;
                    END IF;
                END
            ');

            // Trigger on UPDATE
            DB::unprepared('
                CREATE TRIGGER services_before_update
                BEFORE UPDATE ON services
                FOR EACH ROW
                BEGIN
                    DECLARE rate_value DECIMAL(15,6);
                    SELECT rate INTO rate_value FROM currencies WHERE id = NEW.currency_id LIMIT 1;
                    IF rate_value IS NOT NULL THEN
                        SET NEW.price_usd = NEW.price * rate_value;
                    ELSE
                        SET NEW.price_usd = NEW.price;
                    END IF;
                END
            ');


        DB::statement('
            UPDATE services s
            JOIN currencies c ON s.currency_id = c.id
            SET s.price_usd = s.price * c.rate
        ');
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS services_before_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS services_before_update');
        }

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('price_usd');
        });
    }
};
