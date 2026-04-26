<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeaLogisticsSeeder extends Seeder
{
    public function run()
    {
        // 1. PAÍSES
        $paissos = [
            ['id' => 1, 'nom' => 'España'],
            ['id' => 2, 'nom' => 'Francia'],
            ['id' => 3, 'nom' => 'Estados Unidos'],
            ['id' => 4, 'nom' => 'China'],
            ['id' => 5, 'nom' => 'Japón'],
            ['id' => 6, 'nom' => 'India']
        ];
        DB::statement('SET IDENTITY_INSERT paissos ON');
        DB::table('paissos')->insert($paissos);
        DB::statement('SET IDENTITY_INSERT paissos OFF');

        // 2. CIUDADES
        $ciutats = [
            ['id' => 1, 'nom' => 'Barcelona', 'pais_id' => 1],
            ['id' => 2, 'nom' => 'Marsella', 'pais_id' => 2],
            ['id' => 3, 'nom' => 'Nueva York', 'pais_id' => 3],
            ['id' => 4, 'nom' => 'Los Ángeles', 'pais_id' => 3],
            ['id' => 5, 'nom' => 'Shanghái', 'pais_id' => 4],
            ['id' => 6, 'nom' => 'Tokio', 'pais_id' => 5],
            ['id' => 7, 'nom' => 'Mumbai', 'pais_id' => 6],
        ];
        DB::statement('SET IDENTITY_INSERT ciutats ON');
        DB::table('ciutats')->insert($ciutats);
        DB::statement('SET IDENTITY_INSERT ciutats OFF');

        // 3. PUERTOS
        $ports = [
            ['id' => 1, 'nom' => 'Puerto de Barcelona', 'ciutat_id' => 1],
            ['id' => 2, 'nom' => 'Puerto de Marsella', 'ciutat_id' => 2],
            ['id' => 3, 'nom' => 'Puerto de Nueva York', 'ciutat_id' => 3],
            ['id' => 4, 'nom' => 'Puerto de Los Ángeles', 'ciutat_id' => 4],
            ['id' => 5, 'nom' => 'Puerto de Shanghái', 'ciutat_id' => 5],
            ['id' => 6, 'nom' => 'Puerto de Tokio', 'ciutat_id' => 6],
            ['id' => 7, 'nom' => 'Puerto de Mumbai', 'ciutat_id' => 7],
        ];
        DB::statement('SET IDENTITY_INSERT ports ON');
        DB::table('ports')->insert($ports);
        DB::statement('SET IDENTITY_INSERT ports OFF');

        // 4. TRACKING STEPS
        $steps = [
            ['id' => 1, 'ordre' => 1, 'nom' => 'Recollida en origen'],
            ['id' => 2, 'ordre' => 2, 'nom' => 'Arribada a magatzem de consolidació'],
            ['id' => 3, 'ordre' => 3, 'nom' => 'Despatx de duana d\'exportació'],
            ['id' => 4, 'ordre' => 4, 'nom' => 'Sortida del port d\'origen'],
            ['id' => 5, 'ordre' => 5, 'nom' => 'En trànsit internacional'],
            ['id' => 6, 'ordre' => 6, 'nom' => 'Arribada al port de destí'],
            ['id' => 7, 'ordre' => 7, 'nom' => 'Despatx de duana d\'importació'],
            ['id' => 8, 'ordre' => 8, 'nom' => 'En repartiment (Last mile)'],
            ['id' => 9, 'ordre' => 9, 'nom' => 'Lliurat al client final'],
        ];
        DB::statement('SET IDENTITY_INSERT tracking_steps ON');
        DB::table('tracking_steps')->insert($steps);
        DB::statement('SET IDENTITY_INSERT tracking_steps OFF');

        // 5. INCOTERMS (Específicos de Mar)
        $incoterms = [
            ['id' => 1, 'codi' => 'FAS', 'nom' => 'Free Alongside Ship', 'tracking_step_id' => 4],
            ['id' => 2, 'codi' => 'FOB', 'nom' => 'Free on Board', 'tracking_step_id' => 4],
            ['id' => 3, 'codi' => 'CFR', 'nom' => 'Cost and Freight', 'tracking_step_id' => 6],
            ['id' => 4, 'codi' => 'CIF', 'nom' => 'Cost, Insurance and Freight', 'tracking_step_id' => 6],
        ];
        DB::statement('SET IDENTITY_INSERT Incoterm ON');
        DB::table('Incoterm')->insert($incoterms);
        DB::statement('SET IDENTITY_INSERT Incoterm OFF');

        // 6. TIPUS CARREGA y FLUXES
        DB::table('tipus_carrega')->insert([
            ['tipus' => 'FCL - Contenidor Complet'],
            ['tipus' => 'LCL - Grupatge'],
            ['tipus' => 'Càrrega Perillosa (IMO)'],
            ['tipus' => 'Càrrega Refrigerada (Reefer)'],
        ]);

        DB::table('tipus_fluxes')->insert([
            ['tipus' => 'Importació'],
            ['tipus' => 'Exportació']
        ]);
    }
}
