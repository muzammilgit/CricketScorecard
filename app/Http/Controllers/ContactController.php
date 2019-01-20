<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    //
    public function __construct()
    {
    }

    public function index(DB $db)
    {
        $data["contacts"] = $db::select('SELECT * FROM `contact` WHERE `deleted_at` IS NULL');
        return view('admin.contact', $data);
    }
}
