<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $category =  get_master_product_category("*", [
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
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        $filter = [
            "category" => "ALL",
            "product" => "ALL",
            "model" => "ALL",
            "version" => "ALL",
        ];
        $data = null;
        $total_user_scan = 0;
        $total_qr_scan = 0;
        $total_variant_scan = 0;
        $where = null;
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
        $product = [];
        $model = [];
        $version = [];
        
        if (isset($request->category) && isset($request->product) && isset($request->model) && isset($request->version)) {
            $filter = [
                "category" => $request->category,
                "product" => $request->product,
                "model" => $request->model,
                "version" => $request->version,
            ];

            $where[] = [
                "field_name" => "SCHED_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_product[] = [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_model[] = [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_version[] = [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

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

            $product = get_master_product(["MPRDT_CODE", "MPRDT_TEXT"],$where_product);
            $model = get_master_product_model(["MPRMO_CODE", "MPRMO_TEXT"], $where_model);
            $version = get_master_product_version(["MPRVE_CODE", "MPRVE_TEXT"], $where_version);

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
        return view('home/dashboard',[
            "category" => $category,
            "filter" => $filter,
            "data" => $data,
            "total_user_scan" => $total_user_scan,
            "total_qr_scan" => $total_qr_scan,
            "total_variant_scan" => $total_variant_scan,
            "product" => $product,
            "model" => $model,
            "version" => $version,
        ]);
    }
    public function scan_qr_zeta(Request $request)
    {
        $category =  get_master_product_category("*", [
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
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ]);

        $filter = [
            "category" => "ALL",
            "product" => "ALL",
            "model" => "ALL",
            "version" => "ALL",
        ];
        $data = null;
        $total_user_scan = 0;
        $total_qr_scan = 0;
        $total_variant_scan = 0;
        $where = null;
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
        $product = [];
        $model = [];
        $version = [];
        
        if (isset($request->category) && isset($request->product) && isset($request->model) && isset($request->version)) {
            $filter = [
                "category" => $request->category,
                "product" => $request->product,
                "model" => $request->model,
                "version" => $request->version,
            ];

            $where[] = [
                "field_name" => "LGPRT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_product[] = [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_model[] = [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_version[] = [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            if ($request->category != "ALL") {
                $where[] = [
                    "field_name" => "LGPRT_MPRCAT_CODE",
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
                    "field_name" => "LGPRT_MPRDT_CODE",
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
                    "field_name" => "LGPRT_MPRMO_CODE",
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
                    "field_name" => "LGPRT_MPRVE_CODE",
                    "operator" => "=",
                    "value" => $request->version,
                ];
               
            }

            $product = get_master_product(["MPRDT_CODE", "MPRDT_TEXT"],$where_product);
            $model = get_master_product_model(["MPRMO_CODE", "MPRMO_TEXT"], $where_model);
            $version = get_master_product_version(["MPRVE_CODE", "MPRVE_TEXT"], $where_version);
            $data = std_get([
                "table_name" => "LGPRT",
                "select" => "*",
                "where" => $where,
            ]);
            if ($data != null) {
                for ($i=0; $i < count($data); $i++) { 
                    $user[] = $data[$i]["LGPRT_CST_SCAN_BY"];
                    $qr[] = $data[$i]["LGPRT_QR_CODE"];
                    $variant[] = $data[$i]["LGPRT_MPRVE_CODE"];
                }

                $user_unique = array_unique($user);
                $qr_unique = array_unique($qr);
                $variant_unique = array_unique($variant);

                $total_user_scan = count($user_unique); 
                $total_qr_scan = count($qr_unique); 
                $total_variant_scan = count($variant_unique); 
            }
        }
        return view('home/dashboard_zeta',[
            "category" => $category,
            "filter" => $filter,
            "data" => $data,
            "total_user_scan" => $total_user_scan,
            "total_qr_scan" => $total_qr_scan,
            "total_variant_scan" => $total_variant_scan,
            "product" => $product,
            "model" => $model,
            "version" => $version,
        ]);
    }

    public function location(Request $request)
    {
        $where = null;
        $where[] = [
            "field_name" => "SCHED_MBRAN_CODE",
            "operator" => "=",
            "value" => session("brand_code"),
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
            [
                "field_name" => "MPRDT_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
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
            [
                "field_name" => "MPRMO_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
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
            [
                "field_name" => "MPRVE_MCOMP_CODE",
                "operator" => "=",
                "value" => session("company_code"),
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
    public function download_excel(Request $request)
    {
        $category =  get_master_product_category("*", [
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
            [
                "field_name" => "MPRCA_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ],
        ],true);
        $filter = [
            "category" => "ALL",
            "product" => "ALL",
            "model" => "ALL",
            "version" => "ALL",
        ];
        $data = null;
        $total_user_scan = 0;
        $total_qr_scan = 0;
        $total_variant_scan = 0;
        $where = null;
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
        $product = [];
        $model = [];
        $version = [];
        
        if (isset($request->category) && isset($request->product) && isset($request->model) && isset($request->version)) {
            $filter = [
                "category" => $request->category,
                "product" => $request->product,
                "model" => $request->model,
                "version" => $request->version,
            ];

            $where[] = [
                "field_name" => "SCHED_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_product[] = [
                "field_name" => "MPRDT_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_model[] = [
                "field_name" => "MPRMO_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

            $where_version[] = [
                "field_name" => "MPRVE_MBRAN_CODE",
                "operator" => "=",
                "value" => session("brand_code"),
            ];

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

            $product = get_master_product(["MPRDT_CODE", "MPRDT_TEXT"],$where_product,true);
            $model = get_master_product_model(["MPRMO_CODE", "MPRMO_TEXT"], $where_model,true);
            $version = get_master_product_version(["MPRVE_CODE", "MPRVE_TEXT", "MPRVE_NOTES", "MPRVE_SKU"], $where_version,true);

            $data = std_get([
                "table_name" => "SCHED",
                "select" => [
                    "SCHED_MASCO_CODE",
                    "SCLOG_CST_SCAN_TEXT",
                    "SCLOG_CST_SCAN_TIMESTAMP",
                    "SCDET_MPRCA_TEXT",
                    "SCDET_MPRDT_TEXT",
                    "SCDET_MPRMO_TEXT",
                    "SCDET_MPRVE_TEXT",
                    "SCDET_MPRVE_SKU",
                    "SCLOG_CST_SCAN_LAT",
                    "SCLOG_CST_SCAN_LNG",
                ],
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
            $data_export =array();
            if ($data != null) {
                for ($i=0; $i < count($data); $i++) { 
                    $location_ = $data[$i]["SCLOG_CST_SCAN_LAT"].",".$data[$i]["SCLOG_CST_SCAN_LNG"];
                    array_push($data_export,[
                        $data[$i]["SCHED_MASCO_CODE"],
                        $data[$i]["SCLOG_CST_SCAN_TEXT"],
                        $data[$i]["SCLOG_CST_SCAN_TIMESTAMP"],
                        $location_,
                        $data[$i]["SCDET_MPRCA_TEXT"],
                        $data[$i]["SCDET_MPRDT_TEXT"],
                        $data[$i]["SCDET_MPRMO_TEXT"],
                        $data[$i]["SCDET_MPRVE_TEXT"],
                        $data[$i]["SCDET_MPRVE_SKU"],
                    ]);
                }
            }
        }
        $insert_lgdld_data = [
            "LGDLD_MCOMP_CODE" =>  session("company_code"),
            "LGDLD_MCOMP_NAME" =>  session("company_name"),
            "LGDLD_MBRAN_CODE" =>  session("brand_code"),
            "LGDLD_MBRAN_NAME" => session("brand_name"),
            "LGDLD_MPRCA_CODE" => $category["MPRCA_CODE"],
            "LGDLD_MPRCA_TEXT" => $category["MPRCA_TEXT"],
            "LGDLD_MPRDT_CODE" => $product["MPRDT_CODE"],
            "LGDLD_MPRDT_TEXT" => $product["MPRDT_TEXT"],
            "LGDLD_MPRMO_CODE" => $model["MPRMO_CODE"],
            "LGDLD_MPRMO_TEXT" => $model["MPRMO_TEXT"],
            "LGDLD_MPRVE_CODE" => $version["MPRVE_CODE"],
            "LGDLD_MPRVE_TEXT" => $version["MPRVE_TEXT"],
            "LGDLD_MPRVE_NOTES" => $version["MPRVE_NOTES"],
            "LGDLD_MPRVE_SKU" => $version["MPRVE_SKU"],
            "LGDLD_CREATED_BY" => session("user_id"),
            "LGDLD_CREATED_TEXT" => session("user_name"),
            "LGDLD_CREATED_TIMESTAMP" => date("Y-m-d H:i:s"),
        ];
        $insert_lgdld = std_insert([
            "table_name" => "LGDLD",
            "data" => $insert_lgdld_data
        ]);
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
        // dd($data_export);
        if($request->type=="CSV"){
            return Excel::download(new UsersExport($data_export), 'CekOri Scan List.csv');
        }else{
            return Excel::download(new UsersExport($data_export), 'CekOri Scan List.xlsx');
        }
    }
}
