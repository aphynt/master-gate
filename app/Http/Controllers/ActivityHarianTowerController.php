<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityHarianTowerController extends Controller
{
    //
    public function index()
    {
        return view('activityHarianTower.index');
    }

    public function insert()
    {
        return view('activityHarianTower.insert');
    }
}
