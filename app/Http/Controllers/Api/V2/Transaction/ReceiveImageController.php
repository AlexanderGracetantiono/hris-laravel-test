<?php

namespace App\Http\Controllers\Api\V2\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ReceiveImageController extends Controller
{
    public function index(Request $request){
        $username = null;
        $password = null;

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        } 
        elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
                list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }
        if (is_null($username)) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Unautorized Access';
            die();
        } 
        else {
            if ($username != "revolusi_mental_api_user" || $password != "FzmKgjHIgbgOyPanzEnVUFNbj1Qi2iW51lekxqG8") {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Unautorized Access';
                die();
            }
        }
        $validate = Validator::make($request->all(),[
            "file" => "required|image|mimes:jpeg,png,jpg,png|max:2048",
            "file_name" => "required|max:255",
            "type" => "required|in:karya,suara_kita,profile"
        ]);
        
        if($validate->fails()){
            return response()->json([
                "message" => $validate->errors(),    
                "data" => $request->all(),
                "err_code" => "UPLOAD_VAL_ERR"
            ],400);
        }
        else{
            $file = $request->file('file');
            $filename = $request->file_name;
            if ($request->type == "karya") {
                $upload_dir = "storage/images/karya_pic/";
            }
            else if ($request->type == "suara_kita") {
                $upload_dir = "storage/images/suara_kita_pic/";
            }
            else if ($request->type == "profile") {
                $upload_dir = "storage/images/profile_pic/";
            }
            
            if (!is_writable($upload_dir)) {
                return response()->json([
                    "message" => "Permission Denied",    
                    "data" => $request->all(),
                    "err_code" => "PERMISSION_DENIED"
                ],500);
            }
    
            $file->move($upload_dir, $filename);
            return response()->json([
                "message" => "Sucessfully Saved",    
                "data" => $request->all()
            ],200);
        }
    }
}