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
}
