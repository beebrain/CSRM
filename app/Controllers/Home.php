<?php

namespace App\Controllers;

use App\Models\ConferenceModel;

class Home extends BaseController
{
    public function index(): string
    {
        $confModel = new ConferenceModel();
        $activeConf = $confModel->where('is_active', 1)->first();

        return view('home', [
            'activeConf' => $activeConf
        ]);
    }

    public function runSeeder()
    {
        $db = \Config\Database::connect();
        
        echo "<h3>Checking and updating database schema...</h3>";
        try {
            $db->query("ALTER TABLE users ADD COLUMN affiliation VARCHAR(255) DEFAULT NULL AFTER last_name");
            echo "Added affiliation column successfully.<br>";
        } catch (\Throwable $e) {
            echo "Affiliation column check: " . $e->getMessage() . "<br>";
        }

        echo "<h3>Running SimulationSeeder...</h3>";
        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\SimulationSeeder');
            echo "Simulation seeder run successfully!<br>";
        } catch (\Throwable $e) {
            echo "Seeder error: " . $e->getMessage() . "<br>";
        }
        
        echo "<br><b>Done. Please remove the temporary run-seeder route and method after verification!</b>";
    }
}
