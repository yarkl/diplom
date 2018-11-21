<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 21.11.18
 * Time: 14:28
 */

namespace App\Http\Controllers;


class AboutProjectController extends Controller
{
    public function index(){
        return view('about');
    }
}