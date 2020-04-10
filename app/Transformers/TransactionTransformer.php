<?php

namespace App\Transformers;
use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'identifier'=>(int)$transaction->id,
            'quantity'=>(string)$transaction->quantity,
            'buyer'=>(int)$transaction->buyer_id,
            'product'=>(int)$transaction->product_id,
            'creationDate' => (string)$transaction->careated_at,
            'lastChange'=>(string)$transaction->updated_at,
            'deletedDate'=>isset($transaction->deleted_at) ? (string) $transaction->deleted_at:null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('transactions.show', $transaction->id),
                ],
                [
                    'rel' => 'transaction.categories',
                    'href' => route('transactions.categories.index', $transaction->id),
                ],
                [
                    'rel' => 'transaction.seller',
                    'href' => route('transactions.sellers.index', $transaction->seller_id),
                ],
                [
                    'rel' => 'buyer',
                    'href' => route('buyers.show', $transaction->buyer_id),
                ],
                [
                    'rel' => 'product',
                    'href' => route('products.show', $transaction->product_id),
                ],
            ]
        ];
    }
    public static function orginalAttribute($index)
    {
        $attributes= [
            'identifier'=>'id',
            'quantity'=>'quantity',
           'buyer'=>'buyer_id',
           'product'=>'product_id',
            'creationDate' => 'careated_at',
            'lastChange'=>'updated_at',
            'deletedDate'=>'deleted_at',
        ]; 

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedArrribute($index)
    {
        $attributes= [
            'id'=>'identifier',
            'quantity'=>'quantity',
           'buyer_id'=>'buyer',
           'product_id'=>'product',
            'careated_at' => 'creationDate',
            'updated_at'=>'lastChange',
            'deleted_at'=>'deletedDate',
        ]; 

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    
}
