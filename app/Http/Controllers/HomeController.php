<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 08.11.18
 * Time: 9:49
 */

namespace App\Http\Controllers;


use App\Services\GraphService;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function home(){
        return view('index');
    }
}