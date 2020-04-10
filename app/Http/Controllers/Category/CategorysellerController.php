<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategorysellerController extends ApiController
{
  public function __construct(){
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
       $this->allowedAdminAction();
       $seller = $category->products()
       ->with('seller')
       ->get()
       ->pluck('seller')
       ->unique()
       ->values();
       return $this->showAll($seller);
    }
    
    
}