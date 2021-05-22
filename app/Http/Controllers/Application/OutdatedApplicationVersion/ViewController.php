<?php

namespace App\Http\Controllers\Application\OutdatedApplicationVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = get_master_version();
        return view('application/outdated_application_version/view', ['data' => $data]);
    }
}
