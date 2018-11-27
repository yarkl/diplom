<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 26.11.18
 * Time: 17:01
 */

namespace App\Http\Controllers;


use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController
{
    private $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index(){
        return view('layouts.course');
    }

    public function graph(){
        return $this->service->processArray()->processNodesWithoutChild()->decode();
    }

    public function show(){
        return view('layouts.course');
    }

    public function json(Request $request,$id){
        $this->service->clear()->getRows($id);
        return $this->service->processArray()->processNodesWithoutChild()->decode();
    }

    public function tags(){
        $arr = [];
        foreach ($this->service->processArray()->getLabels() as $label) {
            unset($label['url'], $label['group'], $label['value']);
            $_label['id'] = $label['id'];
            $_label['name'] = $label['label'];
            $_label['show'] = isset($label['show']) ? $label['show'] : '';
            array_push($arr,$_label);
        }
        return $arr;
    }

}