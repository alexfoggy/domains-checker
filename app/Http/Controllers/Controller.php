<?php

namespace App\Http\Controllers;

use App\Domain;
use App\DomainInfo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function deleteDomain($id){
        if($id){
            Domain::where('id',$id)->delete();
            DomainInfo::where('domain_id',$id)->delete();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false]);
    }
}
