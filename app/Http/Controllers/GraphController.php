<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 08.11.18
 * Time: 14:07
 */

namespace App\Http\Controllers;


use App\Services\GraphService;

class GraphController
{
    private $graphService;

    public function __construct(GraphService $service)
    {
        $this->graphService = $service;
    }

    public function graph(){
        return $this->graphService->processArray(2)->decode();
    }

    public function show($id){
        return $this->graphService->processArray($id)->decode();
    }
}