<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function Error($pars)
    {
        return response()->json([
            'success' => 0,
            'message' => $pars
        ]);
    }

    public function login(Request $request)
    {
        $cek = DB::table('users')
            ->where('email','=',$request->email)
            ->first();
        if ($cek) {
            if (password_verify($request->password,$cek->password)) {
                return response()->json([
                    'success' => 1,
                    'message' => 'Login Berhasil, Selamat datang '.$cek->name,
                    'id_user' => $cek->id,
                    'name' => $cek->name,
                    'email' => $cek->email
                ]);
            }else{
                return $this->Error('Password Salah');
            }
        }else {
            return $this->Error('Login gagal, Email tidak terdaftar');
        }
    }

    public function register(Request $request)
    {
        if($request->email == ''){
            return $this->Error('Email Kosong');
        }elseif ($request->password == '') {
            return $this->Error('Password Kosong');
        }

        $user = DB::table('users')->where('email', $request->email)->first();
        //return $user;
        if ($user) {
            return $this->Error('Email '.$request->email.' sudah pernah terdaftar');
        }else {
            $usr = DB::table('users')->insert([
                'name'=> $request->name,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
                'isAdmin' => false
            ]);
    
            if($usr){
                return response()->json([
                    'success' => 1,
                    'message' => 'Berhasil mendaftarkan akun'
                ]);
            }else{
                return $this->Error('Gagal mendaftarkan akun');
            }
        }
    }

    public function userDetail($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            return response()->json([
                'success' => 1,
                'message' => $user
            ]);
        }else {
            return $this->Error('Data user tidak ditemukan');
        }
    }

    public function userUpdate(Request $request)
    {
        if($request->email == ''){
            return $this->Error('Email Kosong');
        }elseif ($request->password == '') {
            return $this->Error('Password Kosong');
        }
        $cek = DB::table('users')->find($request->id);
        if ($cek) {
            $usr = DB::table('users')
                    ->where('id', $request->id)
                    ->update([
                        'name'=> $request->name,
                        'email'=> $request->email,
                        'password'=> Hash::make($request->password),
                        'isAdmin'=> false
                    ]);
    
            if($usr){
                return response()->json([
                    'success' => 1,
                    'message' => 'Berhasil update akun'
                ]);
            }else{
                return $this->Error('Gagal mendaftarkan akun');
            }
        }else {
            return $this->Error('Data tidak ditemukan');
        }
    }
}
