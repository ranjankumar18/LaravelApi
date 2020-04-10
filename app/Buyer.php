<?php

namespace App;
use App\Transformers\BuyerTransformer;
use App\Transaction;
use App\Scopes\BuyerScope;
//use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
   public $transformer = BuyerTransformer::class;
	protected static function boot(){
		parent::boot();
		static::addGlobalScope(new BuyerScope);
	}
    public function transactions(){
		return $this->hasMany(Transaction::class);
	}
    
}
