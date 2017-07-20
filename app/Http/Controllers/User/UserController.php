<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //creo la variabile utente e seleziono tutti
        $users = User::all();

//        return response()->json(['data' => $users], 200);
        return $this->showAll($users);
//        return $users; in questo caso non ho la possibilità di gestire il json

    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validazione dell'utente
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

//        return response()->json(['data' => $user], 201);
        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //mostro utente
        $user = User::findOrFail($id); //cerco l'utente con id

//        return response()->json(['data' => $user], 200);
        return $this->showOne($user, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id); //cerco l'utente con id

        //validazione dell'utente
        $rules = [
            'email' => 'email|unique:users, email, ' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
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
            if(!$user->isVerified()){
                return response()->json(['error' => 'Verificare solo l\'utente non modificare l\'admin' , 'code' => 409], 409);
            }

            $user->admin = $request->admin;
        }

        if(!$user->isDirty()){
            return response()->json(['error' => 'Non hai specificato un valore differente per fare l\'update' , 'code' => 422], 422);
        }

        $user->save();

//        return response()->json(['data' => $user], 200);
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::findOrFail($id);

        $user->delete();

//        return response()->json(['data' => $user], 200);
        return $this->showOne($user);
    }
}
