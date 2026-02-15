<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable PostGIS extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::table('mangrove_locations', function (Blueprint $table) {
            // Add geometry column for polygon/multipolygon data from GeoJSON
            // SRID 4326 = WGS84 (standard for lat/long coordinates)
            DB::statement('ALTER TABLE mangrove_locations ADD COLUMN geometry geometry(Geometry, 4326)');

            // Add spatial index for better performance
            DB::statement('CREATE INDEX mangrove_locations_geometry_idx ON mangrove_locations USING GIST (geometry)');

            // Add metadata columns from GeoJSON properties
            $table->json('geojson_properties')->nullable()->after('geometry');
            $table->string('geojson_source')->nullable()->after('geojson_properties');
        });

        // Create a function to update point geometry from lat/lng
        DB::statement("
            CREATE OR REPLACE FUNCTION update_point_geometry()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.latitude IS NOT NULL AND NEW.longitude IS NOT NULL AND NEW.geometry IS NULL THEN
                    NEW.geometry = ST_SetSRID(ST_MakePoint(NEW.longitude, NEW.latitude), 4326);
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create trigger to auto-update point geometry
        DB::statement("
            CREATE TRIGGER update_mangrove_location_geometry
            BEFORE INSERT OR UPDATE ON mangrove_locations
            FOR EACH ROW
            EXECUTE FUNCTION update_point_geometry();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop trigger and function
        DB::statement('DROP TRIGGER IF EXISTS update_mangrove_location_geometry ON mangrove_locations');
        DB::statement('DROP FUNCTION IF EXISTS update_point_geometry()');

        Schema::table('mangrove_locations', function (Blueprint $table) {
            // Drop spatial index
            DB::statement('DROP INDEX IF EXISTS mangrove_locations_geometry_idx');

            // Drop geometry column
            $table->dropColumn(['geometry', 'geojson_properties', 'geojson_source']);
        });
    }
};
