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
    public function graph(){
        $graphService = new GraphService();
        $graphService->processArray(2);
        //dd($graphService->getLabels());
        return json_encode(['labels' => $graphService->getLabels(),'nodes' => $graphService->getReadyArr()],JSON_UNESCAPED_UNICODE);
    }
}