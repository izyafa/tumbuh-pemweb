<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'pgsql' || DB::getDriverName() === 'pgsql') {
            DB::statement("
                CREATE OR REPLACE FUNCTION uuid_max(uuid, uuid) RETURNS uuid AS $$
                BEGIN
                    RETURN GREATEST($1, $2);
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            DB::statement("
                CREATE OR REPLACE FUNCTION uuid_min(uuid, uuid) RETURNS uuid AS $$
                BEGIN
                    RETURN LEAST($1, $2);
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_aggregate a JOIN pg_proc p ON a.aggfnoid = p.oid WHERE p.proname = 'max' AND p.proargtypes[0] = 'uuid'::regtype) THEN
                        CREATE AGGREGATE max(uuid) (SFUNC = uuid_max, STYPE = uuid);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_aggregate a JOIN pg_proc p ON a.aggfnoid = p.oid WHERE p.proname = 'min' AND p.proargtypes[0] = 'uuid'::regtype) THEN
                        CREATE AGGREGATE min(uuid) (SFUNC = uuid_min, STYPE = uuid);
                    END IF;
                END $$;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql' || DB::getDriverName() === 'pgsql') {
            DB::statement("DROP AGGREGATE IF EXISTS max(uuid);");
            DB::statement("DROP AGGREGATE IF EXISTS min(uuid);");
            DB::statement("DROP FUNCTION IF EXISTS uuid_max(uuid, uuid);");
            DB::statement("DROP FUNCTION IF EXISTS uuid_min(uuid, uuid);");
        }
    }
};
