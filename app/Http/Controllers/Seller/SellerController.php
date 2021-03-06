<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Seller;
class SellerController extends ApiController
{
     public function __construct(){
        parent::__construct();
         $this->middleware('scope:read-general')->only(['show']);
        $this->middleware('can:view,seller')->only('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller = Seller::has('products')->get();
         return $this->showAll($seller);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        // $seller = Seller::has('products')->findOrFail($id);
        return $this->showOne($seller);
    }

    
}
