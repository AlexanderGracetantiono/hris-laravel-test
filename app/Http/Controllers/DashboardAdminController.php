<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $company = get_master_company("*",[
            [
                "field_name" => "MCOMP_STATUS",
                "operator" => "=",
                "value" => "1"
            ],
            [
                "field_name" => "MCOMP_IS_DELETED",
                "operator" => "=",
                "value" => "0"
            ],
        ]);

        $count_brand = std_get([
            "table_name" => "MBRAN",
            "where" => [
                [
                    "field_name" => "MBRAN_STATUS",
                    "operator" => "=",
                    "value" => "1",
                ],
                [
                    "field_name" => "MBRAN_IS_DELETED",
                    "operator" => "=",
                    "value" => "0",
                ]
            ],
            "count" => true,
            "first_row" => true,
        ]);
        
        $filter = [
            "company" => "",
            "brand" => "",
            "category" => "",
            "product" => "",
            "model" => "",
            "version" => "",
        ];
        $selected_brand_type = 0;
        $allow_product_filter = 0;
        $data = null;
        $total_user_scan = 0;
        $total_qr_scan = 0;
        $total_variant_scan = 0;
        $where = null;
        $where_category[] = [
            "field_name" => "MPRCA_STATUS",
            "operator" => "=",
            "value" => "1",
        ];
        $where_product[] = [
            "field_name" => "MPRDT_STATUS",
            "operator" => "=",
            "value" => "1",
        ];
        $where_model[] = [
            "field_name" => "MPRMO_STATUS",
            "operator" => "=",
            "value" => "1",
        ];
        $where_version[] = [
            "field_name" => "MPRVE_STATUS",
            "operator" => "=",
            "value" => "1",
        ];
        $brand = [];
        $category = [];
        $product = [];
        $model = [];
        $version = [];

        if (isset($request->brand)) {
            $allow_product_filter = 1;

            $filter = [
                "company" => $request->company,
                "brand" => $request->brand,
                "category" => $request->category,
                "product" => $request->product,
                "model" => $request->model,
                "version" => $request->version,
            ];

            $brand = get_master_brand(["MBRAN_CODE", "MBRAN_NAME"],[
                [
                    "field_name" => "MBRAN_STATUS",
                    "operator" => "=",
                    "value" => "1",
                ],
                [
                    "field_name" => "MBRAN_IS_DELETED",
                    "operator" => "=",
                    "value" => "0",
                ],
                [
                    "field_name" => "MBRAN_MCOMP_CODE",
                    "operator" => "=",
                    "value" => $request->company,
                ],
            ]);

            $temp_selected_brand_type = get_master_brand(["MBRAN_TYPE"],[
                [
                    "field_name" => "MBRAN_CODE",
                    "operator" => "=",
                    "value" => $request->brand,
                ],
            ],true);

            $selected_brand_type = $temp_selected_brand_type["MBRAN_TYPE"];

            $where_category[] = [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];

            $where_product[] = [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];

            $where_model[] = [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];

            $where_version[] = [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];

            $category = get_master_product_category(["MPRCA_CODE", "MPRCA_TEXT"],$where_category);
            $unshift_data_category = [
                "MPRCA_CODE" => "ALL",
                "MPRCA_TEXT" => "ALL",
            ];
            array_unshift($category,$unshift_data_category);
            

            $product = get_master_product(["MPRDT_CODE", "MPRDT_TEXT"],$where_product);
            $unshift_data_product = [
                "MPRDT_CODE" => "ALL",
                "MPRDT_TEXT" => "ALL",
            ];
            array_unshift($product,$unshift_data_product);

            $model = get_master_product_model(["MPRMO_CODE", "MPRMO_TEXT"], $where_model);
            $unshift_data_model = [
                "MPRMO_CODE" => "ALL",
                "MPRMO_TEXT" => "ALL",
            ];
            array_unshift($model,$unshift_data_model);

            $version = get_master_product_version(["MPRVE_CODE", "MPRVE_TEXT"], $where_version);
            $unshift_data_version = [
                "MPRVE_CODE" => "ALL",
                "MPRVE_TEXT" => "ALL",
            ];
            array_unshift($version,$unshift_data_version);

            if (isset($request->company) && isset($request->brand) && isset($request->category) && isset($request->product) && isset($request->model) && isset($request->version)) {
                
                if ($request->brand) {
                    $where[] = [
                        "field_name" => "SCHED_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->brand,
                    ];

                    $where_category[] = [
                        "field_name" => "MPRCA_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->brand,
                    ];

                    $where_product[] = [
                        "field_name" => "MPRDT_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->brand,
                    ];
        
                    $where_model[] = [
                        "field_name" => "MPRMO_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->brand,
                    ];
        
                    $where_version[] = [
                        "field_name" => "MPRVE_MBRAN_CODE",
                        "operator" => "=",
                        "value" => $request->brand,
                    ];
                }
                if ($request->category != "ALL") {
                    $where[] = [
                        "field_name" => "SCDET_MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request->category,
                    ];

                    $where_product[] = [
                        "field_name" => "MPRDT_MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request->category,
                    ];

                    $where_model[] = [
                        "field_name" => "MPRMO_MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request->category,
                    ];

                    $where_version[] = [
                        "field_name" => "MPRVE_MPRCA_CODE",
                        "operator" => "=",
                        "value" => $request->category,
                    ];
                    
                }
                if ($request->product != "ALL") {
                    $where[] = [
                        "field_name" => "SCDET_MPRDT_CODE",
                        "operator" => "=",
                        "value" => $request->product,
                    ];

                    $where_model[] = [
                        "field_name" => "MPRMO_MPRDT_CODE",
                        "operator" => "=",
                        "value" => $request->product,
                    ];

                    $where_version[] = [
                        "field_name" => "MPRVE_MPRDT_CODE",
                        "operator" => "=",
                        "value" => $request->product,
                    ];

                }
                if ($request->model != "ALL") {
                    $where[] = [
                        "field_name" => "SCDET_MPRMO_CODE",
                        "operator" => "=",
                        "value" => $request->model,
                    ];

                    $where_version[] = [
                        "field_name" => "MPRVE_MPRMO_CODE",
                        "operator" => "=",
                        "value" => $request->model,
                    ];
                    
                }
                if ($request->version != "ALL") {
                    $where[] = [
                        "field_name" => "SCDET_MPRVE_CODE",
                        "operator" => "=",
                        "value" => $request->version,
                    ];
                
                }

                $brand = get_master_brand(["MBRAN_CODE", "MBRAN_NAME"],[
                    [
                        "field_name" => "MBRAN_STATUS",
                        "operator" => "=",
                        "value" => "1",
                    ],
                    [
                        "field_name" => "MBRAN_IS_DELETED",
                        "operator" => "=",
                        "value" => "0",
                    ],
                    [
                        "field_name" => "MBRAN_MCOMP_CODE",
                        "operator" => "=",
                        "value" => $request->company,
                    ],
                ]);

                $data = std_get([
                    "table_name" => "SCHED",
                    "select" => ["*"],
                    "where" => $where,
                    "join" => [
                        [
                            "table_name" => "SCDET",
                            "join_type" => "inner",
                            "on1" => "SCDET_SCHED_ID",
                            "operator" => "=",
                            "on2" => "SCHED_ID",
                        ],
                        [
                            "table_name" => "SCLOG",
                            "join_type" => "inner",
                            "on1" => "SCLOG_SCHED_ID",
                            "operator" => "=",
                            "on2" => "SCHED_ID",
                        ],
                    ]
                ]);

                if ($data != null) {
                    for ($i=0; $i < count($data); $i++) { 
                        $user[] = $data[$i]["SCLOG_CST_SCAN_BY"];
                        $qr[] = $data[$i]["SCHED_TRQRZ_CODE"];
                        $variant[] = $data[$i]["SCDET_MPRVE_CODE"];
                    }

                    $user_unique = array_unique($user);
                    $qr_unique = array_unique($qr);
                    $variant_unique = array_unique($variant);

                    $total_user_scan = count($user_unique); 
                    $total_qr_scan = count($qr_unique); 
                    $total_variant_scan = count($variant_unique); 
                }
            }
        }
        $insert_LGMAP_data = [
            "LGMAP_MCOMP_CODE" =>  session("company_code"),
            "LGMAP_MCOMP_NAME" =>  session("company_name"),
            "LGMAP_MBRAN_CODE" =>  session("brand_code"),
            "LGMAP_MBRAN_NAME" => session("brand_name"),
            "LGMAP_CREATED_BY" => session("user_id"),
            "LGMAP_CREATED_TEXT" => session("user_name"),
            "LGMAP_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_LGMAP = std_insert([
            "table_name" => "LGMAP",
            "data" => $insert_LGMAP_data
        ]);
        return view('home/dashboard_admin',[
            "company" => $company,
            "filter" => $filter,
            "data" => $data,
            "total_user_scan" => $total_user_scan,
            "total_qr_scan" => $total_qr_scan,
            "total_variant_scan" => $total_variant_scan,
            "count_brand" => $count_brand,
            "brand" => $brand,
            "selected_brand_type" => $selected_brand_type,
            "category" => $category,
            "product" => $product,
            "model" => $model,
            "version" => $version,
            "allow_product_filter" => $allow_product_filter,
        ]);
    }

    public function location(Request $request)
    {
        $where = null;
        // $where[] = [
        //     "field_name" => "SCHED_MCOMP_CODE",
        //     "operator" => "=",
        //     "value" => $request->company,
        // ];
        $where[] = [
            "field_name" => "SCHED_MBRAN_CODE",
            "operator" => "=",
            "value" => $request->brand,
        ];

        if ($request->category != "ALL") {
            $where[] = [
                "field_name" => "SCDET_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ];
        }
        if ($request->product != "ALL") {
            $where[] = [
                "field_name" => "SCDET_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ];
        }
        if ($request->model != "ALL") {
            $where[] = [
                "field_name" => "SCDET_MPRMO_CODE",
                "operator" => "=",
                "value" => $request->model,
            ];
        }
        if ($request->version != "ALL") {
            $where[] = [
                "field_name" => "SCDET_MPRVE_CODE",
                "operator" => "=",
                "value" => $request->version,
            ];
        }

        $data = std_get([
            "table_name" => "SCHED",
            "select" => "*",
            "where" => $where,
            "join" => [
                [
                    "table_name" => "SCLOG",
                    "join_type" => "inner",
                    "on1" => "SCLOG_SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCHED_ID",
                ],
                [
                    "table_name" => "SCDET",
                    "join_type" => "inner",
                    "on1" => "SCDET_SCHED_ID",
                    "operator" => "=",
                    "on2" => "SCHED_ID",
                ],
            ]
        ]);
        
        $response = null;

        if ($data != null) {
            for ($i=0; $i < count($data); $i++) { 
                $response[] = [
                    "lat" => $data[$i]["SCLOG_CST_SCAN_LAT"],
                    "lng" => $data[$i]["SCLOG_CST_SCAN_LNG"],
                    "user" => $data[$i]["SCLOG_CST_SCAN_TEXT"],
                    "scan_time" => $data[$i]["SCLOG_CST_SCAN_TIMESTAMP"],
                    "category" => $data[$i]["SCDET_MPRCA_TEXT"],
                    "product" => $data[$i]["SCDET_MPRDT_TEXT"],
                    "model" => $data[$i]["SCDET_MPRMO_TEXT"],
                    "version" => $data[$i]["SCDET_MPRVE_TEXT"],
                    "sku" => $data[$i]["SCDET_MPRVE_SKU"],
                ];
            }
        }
        return response()->json($response, 200);
    }
    public function location_zeta(Request $request)
    {
        $where = null;
        // $where[] = [
        //     "field_name" => "SCHED_MCOMP_CODE",
        //     "operator" => "=",
        //     "value" => $request->company,
        // ];
        $where[] = [
            "field_name" => "LGPRT_MBRAN_CODE",
            "operator" => "=",
            "value" => $request->brand,
        ];

        if ($request->category != "ALL") {
            $where[] = [
                "field_name" => "LGPRT_MPRCAT_CODE",
                "operator" => "=",
                "value" => $request->category,
            ];
        }
        if ($request->product != "ALL") {
            $where[] = [
                "field_name" => "LGPRT_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ];
        }
        if ($request->model != "ALL") {
            $where[] = [
                "field_name" => "LGPRT_MPRMO_CODE",
                "operator" => "=",
                "value" => $request->model,
            ];
        }
        if ($request->version != "ALL") {
            $where[] = [
                "field_name" => "LGPRT_MPRVE_CODE",
                "operator" => "=",
                "value" => $request->version,
            ];
        }

        $data = std_get([
            "table_name" => "LGPRT",
            "select" => "*",
            "where" => $where,
        ]);
        
        $response = null;

        if ($data != null) {
            for ($i=0; $i < count($data); $i++) { 
                $response[] = [
                    "lat" => $data[$i]["LGPRT_CST_SCAN_LAT"],
                    "lng" => $data[$i]["LGPRT_CST_SCAN_LNG"],
                    "user" => $data[$i]["LGPRT_CST_SCAN_TEXT"],
                    "scan_time" => $data[$i]["LGPRT_CST_SCAN_TIMESTAMP"],
                    "category" => $data[$i]["LGPRT_MPRCAT_TEXT"],
                    "product" => $data[$i]["LGPRT_MPRDT_TEXT"],
                    "model" => $data[$i]["LGPRT_MPRMO_TEXT"],
                    "version" => $data[$i]["LGPRT_MPRVE_TEXT"],
                    // "sku" => $data[$i]["LGPRT_MPRVE_SKU"],
                ];
            }
        }
        return response()->json($response, 200);
    }

    public function brand(Request $request)
    {
        $brand = get_master_brand(["MBRAN_CODE as id", "MBRAN_NAME as text"], [
            [
                "field_name" => "MBRAN_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MBRAN_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
            [
                "field_name" => "MBRAN_MCOMP_CODE",
                "operator" => "=",
                "value" => $request->company,
            ],
        ]);

        echo json_encode($brand);
    }

    public function category(Request $request)
    {
        $where = [
            [
                "field_name" => "MPRCA_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRCA_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ];
        if ($request->brand != "ALL") {
            $where[] = [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];
        }
        $product = get_master_product_category(["MPRCA_CODE as id", "MPRCA_TEXT as text"],$where);

        $unshift_data = [
            "id" => "ALL",
            "text" => "ALL",
        ];
        array_unshift($product,$unshift_data);

        echo json_encode($product);
    }

    public function product(Request $request)
    {
        $where = [
            [
                "field_name" => "MPRDT_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRDT_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ];
        if ($request->brand != "ALL") {
            $where[] = [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];
        }
        if ($request->category != "ALL") {
            $where[] = [
                "field_name" => "MPRDT_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ];
        }
        $product = get_master_product(["MPRDT_CODE as id", "MPRDT_TEXT as text"],$where);

        $unshift_data = [
            "id" => "ALL",
            "text" => "ALL",
        ];
        array_unshift($product,$unshift_data);

        echo json_encode($product);
    }

    public function model(Request $request)
    {
        $where = [
            [
                "field_name" => "MPRMO_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRMO_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ];
        if ($request->brand != "ALL") {
            $where[] = [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];
        }
        if ($request->category != "ALL") {
            $where[] = [
                "field_name" => "MPRMO_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ];
        }
        if ($request->product != "ALL") {
            $where[] = [
                "field_name" => "MPRMO_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ];
        }
        $model = get_master_product_model(["MPRMO_CODE as id", "MPRMO_TEXT as text"], $where);

        $unshift_data = [
            "id" => "ALL",
            "text" => "ALL",
        ];
        array_unshift($model,$unshift_data);

        echo json_encode($model);
    }

    public function version(Request $request)
    {
        $where = [
            [
                "field_name" => "MPRVE_STATUS",
                "operator" => "=",
                "value" => "1",
            ],
            [
                "field_name" => "MPRVE_IS_DELETED",
                "operator" => "=",
                "value" => "0",
            ],
        ];
        if ($request->brand != "ALL") {
            $where[] = [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => $request->brand,
            ];
        }
        if ($request->category != "ALL") {
            $where[] = [
                "field_name" => "MPRVE_MPRCA_CODE",
                "operator" => "=",
                "value" => $request->category,
            ];
        }
        if ($request->product != "ALL") {
            $where[] = [
                "field_name" => "MPRVE_MPRDT_CODE",
                "operator" => "=",
                "value" => $request->product,
            ];
        }
        if ($request->model != "ALL") {
            $where[] = [
                "field_name" => "MPRVE_MPRMO_CODE",
                "operator" => "=",
                "value" => $request->model,
            ];
        }
        $version = get_master_product_version(["MPRVE_CODE as id", "MPRVE_TEXT as text"], $where);

        $unshift_data = [
            "id" => "ALL",
            "text" => "ALL",
        ];
        array_unshift($version,$unshift_data);

        echo json_encode($version);
    }
}
