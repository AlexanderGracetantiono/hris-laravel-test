<?php

namespace App\Http\Controllers\MasterData\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([1]); 
    }
    
    public function index(Request $request)
    {
        $company = get_master_company();
        $data = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => $request->code,
            ]
        ],true);

        if ($data["MBRAN_TYPE"] == 1) {
            $option = [
                [
                    "id" => "1",
                    "text" => "Category",
                ],
                [
                    "id" => "2",
                    "text" => "Product",
                ],
                [
                    "id" => "3",
                    "text" => "Model",
                ],
                [
                    "id" => "4",
                    "text" => "Version",
                ],
            ];
        } elseif ($data["MBRAN_TYPE"] == 2) {
            $option = [
                [
                    "id" => "1",
                    "text" => "Test Lab Type",
                ],
            ];
        }

        return view('master_data/brand/edit', [
            'data' => $data,
            'company' => $company,
            'option' => $option,
        ]);
    }

    public function validate_input($request)
    {
        $validate = Validator::make($request->all(),[
            "MBRAN_MCOMP_CODE" => "required",
            "MBRAN_NAME" => "required|max:255",
            "MBRAN_ADDRESS" => "required",
            "MBRAN_IMAGE" => "image|mimes:jpeg,png,jpg,png|max:3072|dimensions:max_width:300,max_height:300",
            "MBRAN_TYPE" => "required",
        ]);

        $attributeNames = [
            "MBRAN_NAME" => "Brand Name",
            "MBRAN_MCOMP_CODE" => "Company",
            "MBRAN_ADDRESS" => "Brand Address",
            "MBRAN_IMAGE" => "Brand Logo",
            "MBRAN_TYPE" => "Brand Type",
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

        if ($request->MBRAN_TYPE == 1) {
            $type = 4;
        }
        if ($request->MBRAN_TYPE == 2) {
            $type = 1;
        }
        if($request->MBRAN_NAME_ORIGINAL != $request->MBRAN_NAME){
            $brand = get_master_product_brand("*",[
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request->MBRAN_CODE
                ],
            ],true);
            $company = get_master_company("*",[
                [
                    "field_name" => "MCOMP_CODE",
                    "operator" => "=",
                    "value" => $brand["MBRAN_MCOMP_CODE"]
                ],
                [
                    "field_name" => "MCOMP_IS_DELETED",
                    "operator" => "=",
                    "value" => 0
                ],
            ],true);
            //send email to company PIC
            $to_name = $company["MCOMP_PIC_NAME"];
            $to_email =  $company["MCOMP_PIC_EMAIL"];
            $data = array(
                "name" => $company["MCOMP_PIC_NAME"],
                "brand_name_original"=>$request->MBRAN_NAME_ORIGINAL,
                "brand_name_changed"=>$request->MBRAN_NAME,
                "company_name"=>$brand["MBRAN_MCOMP_NAME"],
            );
                Mail::send("mail.change_brand_name", ['data' => $data], function ($message) use ($to_name, $to_email) {
                    $message
                        ->to($to_email, $to_name)
                        ->subject("Change Brand Name");
                    $message->from("admin@cekori.com", "Change Brand Name.");
                });
                $insert_lgema_data = [
                    // "LGEMA_EMP_CODE" =>  $company["MCOMP_PIC_NAME"],
                    "LGEMA_EMP_NAME" =>  $company["MCOMP_PIC_NAME"],
                    "LGEMA_EMP_EMAIL" =>  $company["MCOMP_PIC_EMAIL"],
                    "LGEMA_COMP_CODE" =>  $company["MCOMP_CODE"],
                    "LGEMA_COMP_NAME" => $company["MCOMP_NAME"],
                    "LGEMA_STATUS" => 6,
                    "LGEMA_CREATED_BY" =>  $company["MCOMP_PIC_NAME"],
                    "LGEMA_CREATED_TEXT" => $company["MCOMP_PIC_NAME"],
                    "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_lgema = std_insert([
                    "table_name" => "LGEMA",
                    "data" => $insert_lgema_data
                ]);
            //send email to employee in brand
            $master_employee_data = std_get([
                "select" => "*",
                "table_name" => "MAEMP",
                "order_by" => [
                    [
                        "field" => "MAEMP_ID",
                        "type" => "DESC",
                    ]
                ],
                "where" => [
                    [
                        "field_name" => "MAEMP_IS_DELETED",
                        "operator" => "=",
                        "value" => "0"
                    ],
                    [
                        "field_name" => "MAEMP_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->MBRAN_CODE
                    ],
                ],
            ]);
            for ($i=0; $i < count($master_employee_data); $i++) {
            $to_name = $master_employee_data[$i]["MAEMP_TEXT"];
            $to_email = $master_employee_data[$i]["MAEMP_EMAIL"];
            $data = array(
                "name" => $master_employee_data[$i]["MAEMP_TEXT"],
                "brand_name_original"=>$request->MBRAN_NAME_ORIGINAL,
                "brand_name_changed"=>$request->MBRAN_NAME,
                "company_name"=>$brand["MBRAN_MCOMP_NAME"],
            );
    
                Mail::send("mail.change_brand_name", ['data' => $data], function ($message) use ($to_name, $to_email) {
                    $message
                        ->to($to_email, $to_name)
                        ->subject("Change Brand Name");
                    $message->from("admin@cekori.com", "Change Brand Name.");
                });
                $insert_lgema_data = [
                    "LGEMA_EMP_CODE" => $master_employee_data[$i]["MAEMP_CODE"],
                    "LGEMA_EMP_NAME" => $master_employee_data[$i]["MAEMP_TEXT"],
                    "LGEMA_EMP_EMAIL" => $master_employee_data[$i]["MAEMP_EMAIL"],
                    "LGEMA_COMP_CODE" => $master_employee_data[$i]["MAEMP_MCOMP_CODE"],
                    "LGEMA_COMP_NAME" =>$master_employee_data[$i]["MAEMP_MCOMP_NAME"],
                    "LGEMA_STATUS" => 6,
                    "LGEMA_CREATED_BY" => $master_employee_data[$i]["MAEMP_CODE"],
                    "LGEMA_CREATED_TEXT" =>$master_employee_data[$i]["MAEMP_TEXT"],
                    "LGEMA_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
                $insert_lgema = std_insert([
                    "table_name" => "LGEMA",
                    "data" => $insert_lgema_data
                ]);
            // } catch (\Exception $e) {
            //     Log::critical("Error when send email via sendinblue");
            //     return response()->json($e, 400);
            // }
            }
        }
        $update_data = [
            "MBRAN_NAME" => $request->MBRAN_NAME,
            "MBRAN_EMAIL" => $request->MBRAN_EMAIL,
            "MBRAN_TYPE" => $request->MBRAN_TYPE,
            "MBRAN_ADDRESS" => $request->MBRAN_ADDRESS,
            "MBRAN_TRPAT_TYPE" => $type,
            "MBRAN_STATUS" => $request->MBRAN_STATUS,
            "MBRAN_UPDATED_BY" => session("user_code"),
            "MBRAN_UPDATED_TEXT" => session("user_name"),
            "MBRAN_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];

        if (isset($request->MBRAN_IMAGE)) {
            $file = $request->file('MBRAN_IMAGE');
            $filename = date("Ymdhis").".".$file->getClientOriginalExtension();
            $upload_dir = "public/storage/images/brand_logo/";

            if (!is_writable($upload_dir)) {
                return response()->json([
                    'message' => "Storage error, please check existing location"
                ],500);
            }
            $file->move($upload_dir, $filename);

            $update_data["MBRAN_IMAGE"] = $filename;
        }

        $update_res = std_update([
            "table_name" => "MBRAN",
            "where" => ["MBRAN_CODE" => $request->MBRAN_CODE],
            "data" => $update_data
        ]);

        if ($update_res === false) {
            return response()->json([
                'message' => "There was an error saving the brand data, please try again for a few moments"
            ],500);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
