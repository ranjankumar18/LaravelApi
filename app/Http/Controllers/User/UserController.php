<?php

namespace App\Http\Controllers\User;
use App\User;
use App\Transformers\UserTransformer;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{

    public function __construct(){
        $this->middleware('auth:api')->except(['store', 'resend','verify']);
      
        $this->middleware('client.credentials')->only(['store','resend']);
        
    
        $this->middleware('tranform.input:'.UserTransformer::class)->only(['store','update']);
        $this->middleware('scopes:manage-account')->only(['show','update']);
        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
         $this->allowedAdminAction();
        $users = User::all();

        return $this->showAll($users);
        //return $users;
    }
    

     public function me(Request $request)
    {
         
        $users = $request->user();
        return $this->showOne($users);
       
        //return $users;
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules =[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token']= User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $users = User::create($data);
        return $this->showOne($users, 201);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //$user = User::findorFail($id);
       return $this->showOne($user);
        //return $users;
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
         
        //$user = User::findOrFail($id);
        $rules =[
            'email' => 'email|unique:users,email,' .$user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' .User::ADMIN_USER .',' .User::REGULAR_USER,
        ];

        if($request->has('name')){
            $user->name = $request->name;
        }

        if($request->has('email') && $user->email != $request->email){
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
             $user->email = $request->email;

        }

        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }

         if($request->has('admin')){
            $this->allowedAdminAction();
            if(!$user->isVerified()){
              return $this->errorResponse('Only the verified user can modify admin field',409);
         
            }

            $user->admin = $request->admin;
           
        }

        if(!$user->isDirty()){
            return $this->errorResponse('You need to specify diffrent value to update.', 422);
          
        }
        
        $user->save();
       return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //$user = User::findOrFail($id);
        $user->delete();
        return $this->showOne($user);
    }

     public function verify($token)
    {
        $user = User::where('verification_token',$token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return $this->showMessages('The account has been verified.');
    }

    public function resend(User $user)
    {

        if($user->isVerified()){
            return $this->errorResponse('This user is already verified.',409);
        }
         retry(5, function() use ($user) {
        Mail::to($user)->send(new UserCreated($user));
         }, 100);
        return $this->showMessages('The verfication mail has been resend.');
    }
}
