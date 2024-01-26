<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DbToolsController extends Controller
{
    public function index()
    {
        return view('admin.dbtools.dbtools');
    }

    public function syncDbStorage()
    {
        return view('admin.dbtools.syncdbstorage');
    }
}
