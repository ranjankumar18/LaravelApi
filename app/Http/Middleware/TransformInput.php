<?php

namespace App\Http\Middleware;

use Closure;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {

        $transformerInput = [];

        foreach ($request->request->all() as $input => $value) {
            $transformerInput[$transformer::originalAttribute($input)] = $value;

            # code...
        }
        $request->replace($transformerInput);
        $response = $next($request);

        if(isset($response->exception) && $response->exception instanceof ValidationException){
           $data = $response->getData();
           $transformedErrors = [];
           foreach ($data->error as  $field=> $error) {
               $traformedField = $transformer::transformedAttribute($field);
               $transformedErrors[$traformedField]=str_replace($field, $traformedField, $error);
           }
           $data->error = $transformedErrors;
           $response->setData($data);

        }

        return $response;
    }
}
