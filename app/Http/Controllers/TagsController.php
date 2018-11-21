<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 21.11.18
 * Time: 11:44
 */

namespace App\Http\Controllers;


use App\Services\GraphService;

class TagsController extends Controller
{
    private $graphService;

    public function __construct(GraphService $service)
    {
        $this->graphService = $service;
        $this->graphService->processArray(2);
    }

    public function index(){
        $arr = [];
        foreach ($this->graphService->getLabels() as $label) {
            unset($label['url'], $label['group'], $label['value']);
            $_label['value'] = $label['id'];
            $_label['label'] = $label['label'];
            array_push($arr,$_label);
        }
        return $arr;
    }
}