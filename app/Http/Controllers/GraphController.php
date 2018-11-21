<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 08.11.18
 * Time: 14:07
 */

namespace App\Http\Controllers;


use App\Services\GraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function show(){
        return view('category');
    }

    public function json(Request $request,$id){
        return $this->graphService->processArray($id)->removeParentTo()->decode();
    }

    public function restore(){
        $resuslts =  DB::select("SELECT source_id,found_id FROM CinC");
        DB::transaction(function () use ($resuslts) {
            foreach ($resuslts as $result){
                DB::insert("INSERT INTO graph (source_id,found_id,type) VALUES ({$result->source_id},{$result->found_id},'cinc')");
                echo "DONE\n";

            }
        });
        $resuslts =  DB::select("SELECT from_id,to_id FROM CtoC WHERE CF > 0.7");
        DB::transaction(function () use ($resuslts) {
            foreach ($resuslts as $result){
                if(null == DB::select("SELECT source_id,found_id FROM graph WHERE source_id = {$result->to_id} AND found_id = {$result->from_id}")){
                    DB::insert("INSERT INTO graph (source_id,found_id,type) VALUES ({$result->to_id},{$result->from_id},'ctoc')");
                    echo "DONE";
                }
            }
        });

    }

    public function parent(){
        $resuslts =  DB::select("SELECT source_id,found_id FROM graph");
        foreach ($resuslts as $resuslt){

        }
    }
}