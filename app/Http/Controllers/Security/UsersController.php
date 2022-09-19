<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class UsersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function showUsers(){
        // $usuarios = User::select('*')->where('is_active',1)->get();
        $form_save = '/register';
        return view('security.users.lista', compact('form_save','form_save'));
    }

    public function getListUsers(Request $request){
        $usuarios = User::select('*')->where('is_active',1)->get();
        $response = [
            'status' => 200,
            'data' => $usuarios
        ];
        return json_encode($response);
    }

    public function getUserById(Request $request){
        $id = $request->get('id');
        $usuario = User::from('users as u')
                    ->select('u.*','r.chr_nombre as rol')
                    ->join('config_rol as r', 'r.id', '=', 'u.role')
                    ->where('u.is_active',1)
                    ->where('u.id',$id)->first();
        $response = [
            'status' => 200,
            'data' => $usuario
        ];
        return json_encode($response);
    }

    public function saveUser(Request $request){
        // dd($request->all());
        $result = User::create([
            'name' => $request->post('name'),
            'role' => $request->post('role'),
            'email' => $request->post('email'),
            'password' => Hash::make($request->post('password')),
        ]);

        $response = [
            'status' => 200,
            'response' => $result
        ];
        return json_encode($response);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
