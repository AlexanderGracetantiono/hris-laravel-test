<?php

namespace App\Http\Controllers\MasterData\Companies;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index(Request $request)
    {
        $data = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        $code = std_get([
            "table_name" => "MACOP",
            "select" => "*",
        ]);

        return view('master_data/companies/company/edit',[
            "data" => $data,
            "code" => $code
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MCOMP_TYPE" => "required",
            "MCOMP_NAME" => "required",
            "MCOMP_NPWP_NUMBER" => "required|digits:15",
            "MCOMP_MACOP_CODE" => "required|exists:MACOP,MACOP_CODE",
            "MCOMP_AREA_NUMBER" => "required",
            "MCOMP_OFFICE_PHONE_NUMBER" => "required|numeric",
            "MCOMP_NPWP" => "image|mimes:jpeg,png,jpg,png|max:3072",
            "MCOMP_OFFICE_ADDRESS" => "required",
            "MCOMP_PIC_NAME" => "required",
            "MCOMP_PIC_EMAIL" => "required|email|unique:MCOMP,MCOMP_PIC_EMAIL,".$request->MCOMP_CODE.",MCOMP_CODE",
            "MCOMP_PIC_MACOP_CODE" => "required|exists:MACOP,MACOP_CODE",
            "MCOMP_PIC_PHONE_NUMBER" => "required|numeric|unique:MCOMP,MCOMP_PIC_PHONE_NUMBER,".$request->MCOMP_CODE.",MCOMP_CODE",
        ]);

        $attributeNames = [
            "MCOMP_TYPE" => "Company Type",
            "MCOMP_NAME" => "Company Name",
            "MCOMP_NPWP_NUMBER" => "Company NPWP Number",
            "MCOMP_NPWP" => "Company NPWP File",
            "MCOMP_MACOP_CODE" => "Company Phone Number - Code",
            "MCOMP_AREA_NUMBER" => "Company Phone Number - Area",
            "MCOMP_OFFICE_PHONE_NUMBER" => "Company Phone Number",
            "MCOMP_PIC_NAME" => "PIC Name",
            "MCOMP_PIC_EMAIL" => "PIC Email",
            "MCOMP_PIC_MACOP_CODE" => "PIC Mobile Number - Code",
            "MCOMP_PIC_PHONE_NUMBER" => "PIC Mobile Number",
            "MCOMP_OFFICE_ADDRESS" => "Company Address",
            "MCOMP_NPWP" => "Upload Company NPWP",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function validate_input_pic($request)
    {
        $validate = Validator::make($request->all(),[
            "MAEMP_USER_NAME" => "required",
            "password" => "",
            "password_confirmation" => "same:password",
        ]);

        $attributeNames = [
            "MAEMP_USER_NAME" => "PIC User Name",
        ];

        $validate->setAttributeNames($attributeNames);
        if($validate->fails()){
            $errors = $validate->errors();
            return $errors->all();
        }
        return true;
    }

    public function update(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }
        // if($request->MCOMP_PIC_EMAIL_ORIGINAL != $request->MCOMP_PIC_EMAIL){
            $to_name = $request->MCOMP_PIC_NAME_ORIGINAL;
            $to_email = $request->MCOMP_PIC_EMAIL_ORIGINAL;
            $verif_code=Str::random(8);
            $parameter = [
                "MCOMP_TYPE" => $request->MCOMP_TYPE,
                "MCOMP_NAME" => $request->MCOMP_NAME,
                "MCOMP_MACOP_CODE" => $request->MCOMP_MACOP_CODE,
                "MCOMP_AREA_NUMBER" => $request->MCOMP_AREA_NUMBER,
                "MCOMP_OFFICE_PHONE_NUMBER" => $request->MCOMP_OFFICE_PHONE_NUMBER,
                "MCOMP_PIC_NAME" => $request->MCOMP_PIC_NAME,
                "MCOMP_PIC_EMAIL" => $request->MCOMP_PIC_EMAIL,
                "MCOMP_PIC_MACOP_CODE" => $request->MCOMP_PIC_MACOP_CODE,
                "MCOMP_PIC_PHONE_NUMBER" => $request->MCOMP_PIC_PHONE_NUMBER,
                "MCOMP_OFFICE_ADDRESS" => $request->MCOMP_OFFICE_ADDRESS,
                "MCOMP_NPWP_NUMBER" => $request->MCOMP_NPWP_NUMBER,
                "MCOMP_NPWP" => null,
                "MCOMP_STATUS" => $request->MCOMP_STATUS,
                'MCOMP_CODE' => $request->MCOMP_CODE,
            ];
            if (isset($request->MCOMP_NPWP)) {
                $file = $request->file('MCOMP_NPWP');
                $filename = date("Ymdhis").".".$file->getClientOriginalExtension();
                // $upload_dir = "public/storage/images/company_npwp/";
                $upload_dir = "storage/images/company_npwp/"; //localhostonly
    
                if (!is_writable($upload_dir)) {
                    return response()->json([
                        'message' => "Storage error, please check existing location"
                    ],500);
                }
                $file->move($upload_dir, $filename);
    
                $parameter["MCOMP_NPWP"] = $filename;
            }
            json_encode($parameter);
            $address = str_split($parameter["MCOMP_OFFICE_ADDRESS"], 60);
            $parameter["link"] =  url('/') . "/master_data/companies/company/update_detail?token=" . $request->MCOMP_CODE."&verif_code=".$verif_code;
       
            try {
                Mail::send("mail.change_email_pic", [
                    'data' => $parameter,
                    'address'=> $address
                ], function ($message) use ($to_name, $to_email) {
                    $message
                        ->to($to_email, $to_name)
                        ->subject("Changes in Company Profile");
                    $message->from("admin@cekori.com", "Changes in Company Profile for ".$to_name);
                });
                $insert_lgema_data = [
                    "LGEMA_EMP_NAME" => $request->MCOMP_PIC_NAME,
                    "LGEMA_EMP_EMAIL" => $request->MCOMP_PIC_EMAIL,
                    "LGEMA_COMP_CODE" => $request->MCOMP_CODE,
                    "LGEMA_COMP_NAME" => $request->MCOMP_NAME,
                    "LGEMA_STATUS" => 6,
                    "LGEMA_CREATED_BY" =>  $request->MCOMP_PIC_NAME,
                    "LGEMA_CREATED_TEXT" =>$request->MCOMP_PIC_NAME,
                    "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_lgema = std_insert([
                    "table_name" => "LGEMA",
                    "data" => $insert_lgema_data
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => "Error when send email via sendinblue"
                ],400);
            }
            $update_data = [
                "MCOMP_VERIF_CODE" => $verif_code,
                "MCOMP_TEMP" => $parameter,
                "MCOMP_UPDATED_BY" => session("user_id"),
                "MCOMP_UPDATED_TEXT" => session("user_name"),
                "MCOMP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $update_res = std_update([
                "table_name" => "MCOMP",
                "where" => ["MCOMP_CODE" =>  $request->MCOMP_CODE],
                "data" => $update_data
            ]);

        return response()->json([
            'message' => "Send email success"
        ],200);
    }
    public function change_company_by_email(Request $request)
    {
        $data = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->token,
            ]
        ],true);
        if ($data['MCOMP_VERIF_CODE'] != $request->verif_code) {
            return abort(404);
        }
        if ($data['MCOMP_TEMP'] == NULL) {
            return abort(404);
        }
            $data_company = json_decode($data['MCOMP_TEMP'],true);
            $update_data = [
                "MCOMP_TYPE" => $data_company['MCOMP_TYPE'],
                "MCOMP_NAME" => $data_company['MCOMP_NAME'],
                "MCOMP_MACOP_CODE" => $data_company['MCOMP_MACOP_CODE'],
                "MCOMP_AREA_NUMBER" => $data_company['MCOMP_AREA_NUMBER'],
                "MCOMP_OFFICE_PHONE_NUMBER" => $data_company['MCOMP_OFFICE_PHONE_NUMBER'],
                "MCOMP_PIC_NAME" => $data_company['MCOMP_PIC_NAME'],
                "MCOMP_PIC_EMAIL" => $data_company['MCOMP_PIC_EMAIL'],
                "MCOMP_PIC_MACOP_CODE" => $data_company['MCOMP_PIC_MACOP_CODE'],
                "MCOMP_PIC_PHONE_NUMBER" => $data_company['MCOMP_PIC_PHONE_NUMBER'],
                "MCOMP_OFFICE_ADDRESS" => $data_company['MCOMP_OFFICE_ADDRESS'],
                "MCOMP_STATUS" => $data_company['MCOMP_STATUS'],
                "MCOMP_NPWP_NUMBER" => $data_company['MCOMP_NPWP_NUMBER'],
                "MCOMP_TEMP" => null,
                "MCOMP_UPDATED_BY" => session("user_id"),
                "MCOMP_UPDATED_TEXT" => session("user_name"),
                "MCOMP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            if ($data_company['MCOMP_NPWP']!=null) {
                $update_data["MCOMP_NPWP"] = $data_company['MCOMP_NPWP'];
            }
            $update_res = std_update([
                "table_name" => "MCOMP",
                "where" => ["MCOMP_CODE" => $request->token],
                "data" => $update_data
            ]);
    
            if ($update_res != true) {
                return response()->json([
                    'message' => "There is something wrong when updating data, please try again"
                ],500);
            }   
            return view("master_data/companies/company/change_company_data_email_success_view");
        
    }

    public function edit_pic(Request $request)
    {
        $data = get_master_company("*",[
            [
                "field_name" => "MCOMP_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        return view('master_data/companies/company/edit_pic',[
            "data" => $data
        ]);
    }

    public function update_pic(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $update_data = [
            "MAEMP_USER_NAME" => $request->MAEMP_USER_NAME,
            "MAEMP_UPDATED_BY" => session("user_id"),
            "MAEMP_UPDATED_TEXT" => session("user_name"),
            "MAEMP_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        if (isset($request->password)) {
            $update_data["MAEMP_PASSWORD"] = Hash::make($request->password);
        }

        $update_res = std_update([
            "table_name" => "MAEMP",
            "where" => ["MCOMP_CODE" => $request->MCOMP_CODE],
            "data" => $update_data
        ]);

        if ($update_res != true) {
            return response()->json([
                'message' => "There is something wrong when updating data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
