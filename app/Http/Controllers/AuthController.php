<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class AuthController extends Controller
{
    private object $output;

    public function __construct()
    {
        $this->output = new stdClass();
        $this->output->response_code = '';
        $this->output->response_desc = '';
    }

    public function register(Request $request): object
    {
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string',
            'username'  => 'required|string|min:3',
            'email'     => 'required|string|email',
            'password'  => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            $this->output->response_code = "422";
            $this->output->response_desc = "Validasi gagal";
            $this->output->errors = $validator->errors();

            return response()->json($this->output, 422);
        }

        try {
            DB::table('data_login')->insert($request->toArray());
            $this->output->response_code = "200";
            $this->output->response_desc = "Berhasil menambahan data user.";

            return response()->json($this->output);
        } catch (Exception $e) {
            throw new Exception("Gagal menambahkan data. " . $e->getMessage());
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => 'required|string',
            'password'  => 'required|string'
        ]);

        if ($validator->fails()) {
            $this->output->response_code = "201";
            $this->output->response_desc = "Validasi gagal";
            $this->output->errors = $validator->errors();

            return response()->json($this->output);
        }
        $cekUser = DB::table('data_login')->where('username', $request->username)->get()->first();
        if(empty($cekUser)){
            $this->output->response_code = "201";
            $this->output->response_desc = "Anda Bukan Admin.";
            return response()->json($this->output);
        }
        $cekPassword = $cekUser->password === $request->password;
        if(empty($cekPassword)){
            $this->output->response_code = "201";
            $this->output->response_desc = "Passwordnya Beda nih.";
            return response()->json($this->output);
        }
        $this->output->response_code = "200";
        $this->output->response_desc = "Berhasil Login";
        return response()->json($this->output);
    }
}
