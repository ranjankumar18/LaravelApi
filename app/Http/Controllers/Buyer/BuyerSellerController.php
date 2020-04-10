<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
{

    public function __construct(){
        parent::__construct();
        $this->middleware('can:view,buyer')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
         $this->allowedAdminAction();
        $seller = $buyer->transactions()->with('product.seller')
        ->get()
        ->pluck('product.seller')
        ->unique('id')
        ->values();
        return $this->showAll($seller);
    }

   
}
