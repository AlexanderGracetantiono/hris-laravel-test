<?php

namespace App\Http\Controllers\Application\LegalVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $data = get_legal_version();
        return view('application/legal_version/view', ['data' => $data]);
    }
}
