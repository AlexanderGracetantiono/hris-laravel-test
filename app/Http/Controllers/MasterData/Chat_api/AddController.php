<?php

namespace App\Http\Controllers\MasterData\Companies;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AddController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]);
    }
    
    public function index()
    {
        $code = std_get([
            "table_name" => "MACOP",
            "select" => "*",
        ]);
        return view('master_data/companies/company/add',["code" => $code]);
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
            "MCOMP_NPWP" => "|required|image|mimes:jpeg,png,jpg,png|max:3072",
            "MCOMP_OFFICE_ADDRESS" => "required",
            "MCOMP_PIC_NAME" => "required",
            "MCOMP_PIC_EMAIL" => "required|email",
            "MCOMP_PIC_MACOP_CODE" => "required|exists:MACOP,MACOP_CODE",
            "MCOMP_PIC_PHONE_NUMBER" => "required|numeric",
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

    public function save(Request $request)
    {
        $validation_res = $this->validate_input($request);
        if ($validation_res !== true) {
            return response()->json([
                'message' => $validation_res
            ],400);
        }

        $check_email = get_master_company("*",[
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "!=",
                "value" => 1
            ],
            [
                "field_name" => "MCOMP_PIC_EMAIL",
                "operator" => "=",
                "value" => $request->MCOMP_PIC_EMAIL
            ],
        ],true);
        if ($check_email != null) {
            return response()->json([
                'message' => "PIC Email already exists"
            ],400);
        }

        $check_phone_number = get_master_company("*",[
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "!=",
                "value" => 1
            ],
            [
                "field_name" => "MCOMP_PIC_MACOP_CODE",
                "operator" => "=",
                "value" => $request->MCOMP_PIC_MACOP_CODE
            ],
            [
                "field_name" => "MCOMP_PIC_PHONE_NUMBER",
                "operator" => "=",
                "value" => $request->MCOMP_PIC_PHONE_NUMBER
            ],
        ],true);
        if ($check_phone_number != null) {
            return response()->json([
                'message' => "PIC phone number already exists"
            ],400);
        }

        $words = explode(" ", $request->MCOMP_NAME);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        if (!isset($acronym[1])) {
            $acronym[1] = "X";
        }
        if (!isset($acronym[2])) {
            $acronym[2] = "X";
        }

        $acronym = strtoupper($acronym);
        $count = std_get([
            "select" => "MCOMP_ID",
            "table_name" => "MCOMP",
            "where" => [
                [
                    "field_name" => "MCOMP_CODE",
                    "operator" => "like",
                    "value" => "%".$acronym."%"
                ]
            ],
            "first_row" => true,
            "count" => true,
        ]);

        $code = $acronym.str_pad($count+1, 4, "0", STR_PAD_LEFT);

        $file = $request->file('MCOMP_NPWP');
        $filename = date("Ymdhis").".".$file->getClientOriginalExtension();
        // $upload_dir = "public/storage/images/company_npwp/";
        $upload_dir = "storage/images/company_npwp/"; //localhostonly
        $verif_code=Str::random(8);
        
        if (!is_writable($upload_dir)) {
            return response()->json([
                'message' => "Storage error, please check existing location"
            ],500);
        }
        $file->move($upload_dir, $filename);
        $insert_data_company = [
            "MCOMP_CODE" => strtoupper($code),
            "MCOMP_TYPE" => $request->MCOMP_TYPE,
            "MCOMP_NAME" => $request->MCOMP_NAME,
            "MCOMP_NPWP" => $filename,
            "MCOMP_NPWP_NUMBER" => $request->MCOMP_NPWP_NUMBER,
            "MCOMP_MACOP_CODE" => $request->MCOMP_MACOP_CODE,
            "MCOMP_AREA_NUMBER" => $request->MCOMP_AREA_NUMBER,
            "MCOMP_OFFICE_PHONE_NUMBER" => $request->MCOMP_OFFICE_PHONE_NUMBER,
            "MCOMP_PIC_NAME" => $request->MCOMP_PIC_NAME,
            "MCOMP_PIC_EMAIL" => $request->MCOMP_PIC_EMAIL,
            "MCOMP_PIC_MACOP_CODE" => $request->MCOMP_PIC_MACOP_CODE,
            "MCOMP_PIC_PHONE_NUMBER" => $request->MCOMP_PIC_PHONE_NUMBER,
            "MCOMP_OFFICE_ADDRESS" => $request->MCOMP_OFFICE_ADDRESS,
            "MCOMP_VERIF_CODE" => $verif_code,
            "MCOMP_STATUS" => 0,
            "MCOMP_IS_DELETED" => 0,
            "MCOMP_CREATED_BY" => session("user_id"),
            "MCOMP_CREATED_TEXT" => session("user_name"),
            "MCOMP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        $insert_res = std_insert([
            "table_name" => "MCOMP",
            "data" => $insert_data_company
        ]);
        $address = str_split($insert_data_company["MCOMP_OFFICE_ADDRESS"], 60);
        $to_name = $insert_data_company["MCOMP_PIC_NAME"];
        $to_email = $insert_data_company["MCOMP_PIC_EMAIL"];
        $company_name = $insert_data_company["MCOMP_TYPE"]." ".$insert_data_company["MCOMP_NAME"];
        try {
            Mail::send("mail.new_company", [
                'data' => $insert_data_company,
                'address' => $address,
                'link'=> url('/') . "/master_data/companies/company/verif_company?token=" .strtoupper($code)."&verif_code=".$verif_code,
            ], function ($message) use ($to_name, $to_email,$company_name) {
                $message
                    ->to($to_email, $to_name)
                    ->subject("Welcome to CekOri ".$company_name." !");
                $message->from("admin@cekori.com", "Welcome to CekOri ".$company_name." !");
            });

            $insert_lgema_data = [
                "LGEMA_COMP_CODE" => strtoupper($code),
                "LGEMA_COMP_NAME" => $insert_data_company["MCOMP_NAME"],
                "LGEMA_CREATED_BY" =>  session("user_code"),
                "LGEMA_CREATED_TEXT" => session("user_name"),
                "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
            ];
            $insert_lgema = std_insert([
                "table_name" => "LGEMA",
                "data" => $insert_lgema_data
            ]);
        } catch (\Exception $e) {
            Log::critical("Mail Company".json_encode($e->getMessage()));
            return response()->json([
                "message" => "Error when send email via sendinblue"
            ], 400);
        }

        if ($insert_res !== true) {
            return response()->json([
                'message' => "There is something wrong when saving data, please try again"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
    public function verif_company(Request $request)
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
        $update_data = [
            "MCOMP_STATUS" => 1,
        ];
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
}
