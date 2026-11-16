<?php

namespace App\Http\Controllers;

use App\Models\ComplaintType;
use App\Models\Office;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'officeCount' => Office::count(),
            'typeCount' => ComplaintType::count(),
        ]);
    }
}