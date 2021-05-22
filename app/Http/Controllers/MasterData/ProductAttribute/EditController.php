<?php

namespace App\Http\Controllers\MasterData\ProductAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __construct() {
        check_is_role_allowed([4]);
    }
    
    public function custom_attribute_view(Request $request)
    {
        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == $request->level) {
            $check_access = true;
        }

        if ($check_access == false) {
            if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
                return redirect('/manufacture/master_data/product/product_categories/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
                return redirect('/manufacture/master_data/product/product/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 3) {
                return redirect('/manufacture/master_data/product/product_model/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 4) {
                return redirect('/manufacture/master_data/product/product_version/view');
            }
        }
        $data_attribute = get_product_attribute("*",[
            [
                "field_name" => "TRPAT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
            [
                "field_name" => "TRPAT_KEY_TYPE",
                "operator" => "=",
                "value" => $check_product_attribute["MBRAN_TRPAT_TYPE"]
            ],
            [
                "field_name" => "TRPAT_KEY_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
            [
                "field_name" => "TRPAT_TYPE",
                "operator" => "=",
                "value" => 2
            ],
        ]);
        $data_general = get_product_attribute("*",[
            [
                "field_name" => "TRPAT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
            [
                "field_name" => "TRPAT_KEY_TYPE",
                "operator" => "=",
                "value" => $check_product_attribute["MBRAN_TRPAT_TYPE"]
            ],
            [
                "field_name" => "TRPAT_KEY_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
            [
                "field_name" => "TRPAT_TYPE",
                "operator" => "=",
                "value" => 1
            ],
        ]);
        $data = std_get([
            "field_name" => "*",
            "table_name" => "MPRVE",
            "where" => [
                [
                    "field_name" => "MPRVE_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ]
                ]);
        return view('master_data/product_attribute/view_custom',[
            "data_attribute" => $data_attribute,
            "data_general" => $data_general,
            "data" => $data[0],
        ]);
    }
    public function index(Request $request)
    {
        $check_product_attribute = get_master_brand("*",[
            [
                "field_name" => "MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ]
        ],true);

        $check_access = false;
        if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == $request->level) {
            $check_access = true;
        }

        if ($check_access == false) {
            if ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 1) {
                return redirect('/manufacture/master_data/product/product_categories/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 2) {
                return redirect('/manufacture/master_data/product/product/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 3) {
                return redirect('/manufacture/master_data/product/product_model/view');
            } elseif ($check_product_attribute["MBRAN_TRPAT_TYPE"] == 4) {
                return redirect('/manufacture/master_data/product/product_version/view');
            }
        }

        $data_general = get_product_attribute("*",[
            [
                "field_name" => "TRPAT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code")
            ],
            [
                "field_name" => "TRPAT_KEY_TYPE",
                "operator" => "=",
                "value" => $check_product_attribute["MBRAN_TRPAT_TYPE"]
            ],
            [
                "field_name" => "TRPAT_KEY_CODE",
                "operator" => "=",
                "value" => $request->code
            ],
            [
                "field_name" => "TRPAT_TYPE",
                "operator" => "=",
                "value" => 1
            ],
        ]);

        // $data_attribute = get_product_attribute("*",[
        //     [
        //         "field_name" => "TRPAT_MBRAN_CODE",
        //         "operator" => "=",
        //         "value" => session("brand_code")
        //     ],
        //     [
        //         "field_name" => "TRPAT_KEY_TYPE",
        //         "operator" => "=",
        //         "value" => $check_product_attribute["MBRAN_TRPAT_TYPE"]
        //     ],
        //     [
        //         "field_name" => "TRPAT_KEY_CODE",
        //         "operator" => "=",
        //         "value" => $request->code
        //     ],
        //     [
        //         "field_name" => "TRPAT_TYPE",
        //         "operator" => "=",
        //         "value" => 2
        //     ],
        // ]);
        $data = std_get([
            "field_name" => "*",
            "table_name" => "MPRVE",
            "where" => [
                [
                    "field_name" => "MPRVE_CODE",
                    "operator" => "=",
                    "value" => $request->code,
                ]
            ]
                ]);
        return view('master_data/product_attribute/view',[
            "data_general" => $data_general,
            "data" => $data[0],
            // "data_attribute" => $data_attribute,
        ]);
    }

    public function update_general(Request $request)
    {
        for ($i=0; $i < count($request->general_id); $i++) { 
            if ($request->general_masking[$i] == null || $request->general_masking[$i] == "") {
                $masking[$i] = $request->general_label[$i];
            } else {
                $masking[$i] = $request->general_masking[$i];
            }

            $update_data[$i] = [
                "TRPAT_MASKING" => $masking[$i],
                "TRPAT_ACTIVE_STATUS" => $request->general_activation[$i],
                "TRPAT_UPDATED_BY" => session("user_code"),
                "TRPAT_UPDATED_TEXT" => session("user_name"),
                "TRPAT_UPDATED_TIMESTAMP" => date("Y-m-d H:i:s")
            ];

            $update = std_update([
                "table_name" => "TRPAT",
                "where" => ["TRPAT_ID" => $request->general_id[$i]],
                "data" => $update_data[$i]
            ]);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }

    public function update_custom(Request $request)
    {
        $custom_attribute = null;

        if (isset($request->old_custom_masking)) {
            $old_custom_masking = $request->old_custom_masking;
            $old_custom_value = $request->old_custom_value;

            for ($i=0; $i < count($old_custom_masking); $i++) { 
                if ($old_custom_masking[$i] == null) {
                    return response()->json([
                        'message' => "Please insert masking for old custom attribute : ".($i+1)
                    ], 400);
                }
                if ($old_custom_value[$i] == null) {
                    return response()->json([
                        'message' => "Please insert value for old custom attribute : ".($i+1)
                    ], 400);
                }

                $custom_attribute[] = [
                    "TRPAT_MCOMP_CODE" => session('company_code'),
                    "TRPAT_MCOMP_NAME" => session('company_name'),
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_MBRAN_NAME" => session('brand_name'),
                    "TRPAT_KEY_TYPE" => $request->KEY_TYPE,
                    "TRPAT_KEY_CODE" => $request->KEY_CODE,
                    "TRPAT_MASKING" => $old_custom_masking[$i],
                    "TRPAT_VALUE" => $old_custom_value[$i],
                    "TRPAT_ACTIVE_STATUS" => "1",
                    "TRPAT_TYPE" => "2",
                    "TRPAT_CREATED_BY" => session("user_id"),
                    "TRPAT_CREATED_TEXT" => session("user_name"),
                    "TRPAT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }

        if (isset($request->CUSTOM)) {
            $new_custom = $request->CUSTOM;

            for ($i=0; $i < count($new_custom); $i++) { 
                if ($new_custom[$i]["new_custom_masking"] == null) {
                    return response()->json([
                        'message' => "Please insert masking for new custom attribute : ".($i+1)
                    ], 400);
                }
                if ($new_custom[$i]["new_custom_value"] == null) {
                    return response()->json([
                        'message' => "Please insert value for new custom attribute : ".($i+1)
                    ], 400);
                }

                $custom_attribute[] = [
                    "TRPAT_MCOMP_CODE" => session('company_code'),
                    "TRPAT_MCOMP_NAME" => session('company_name'),
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_MBRAN_NAME" => session('brand_name'),
                    "TRPAT_KEY_TYPE" => $request->KEY_TYPE,
                    "TRPAT_KEY_CODE" => $request->KEY_CODE,
                    "TRPAT_MASKING" => $new_custom[$i]["new_custom_masking"],
                    "TRPAT_VALUE" => $new_custom[$i]["new_custom_value"],
                    "TRPAT_ACTIVE_STATUS" => "1",
                    "TRPAT_TYPE" => "2",
                    "TRPAT_CREATED_BY" => session("user_id"),
                    "TRPAT_CREATED_TEXT" => session("user_name"),
                    "TRPAT_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
                ];
            }
        }

        if ($custom_attribute != null) {
            std_delete([
                "table_name" => "TRPAT",
                "where" => [
                    "TRPAT_MBRAN_CODE" => session('brand_code'),
                    "TRPAT_KEY_TYPE" => $request->KEY_TYPE,
                    "TRPAT_KEY_CODE" => $request->KEY_CODE,
                    "TRPAT_TYPE" => "2",
                ],
            ]);

            std_insert([
                "table_name" => "TRPAT",
                "data" => $custom_attribute
            ]);
        }

        return response()->json([
            'message' => "OK"
        ],200);
    }
}
