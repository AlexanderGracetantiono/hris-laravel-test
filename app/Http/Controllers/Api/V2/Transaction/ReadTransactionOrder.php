<?php

namespace App\Http\Controllers\Api\V2\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReadTransactionOrder extends Controller
{
    public function get(Request $request)
    {
        if (isset($request->selects)) {
            $select_fields = $request->selects;
        } else {
            $select_fields = ["*"];
        }

        if (isset($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = NULL;
        }

        if (isset($request->offset)) {
            $offset = $request->offset;
        } else {
            $offset = NULL;
        }

        if (isset($request->is_find) && $request->is_find == true) {
            $first_row = true;
        } else {
            $first_row = false;
        }
        $conditions = NULL;
        if (isset($request->conditions) && is_array($request->conditions)) {
            $conditions = [];
            for ($i = 0; $i < count($request->conditions); $i++) {
                if (isset($request->conditions[$i]["field_name"]) && isset($request->conditions[$i]["operator"]) && isset($request->conditions[$i]["value"])) {
                    $conditions[$i]["field_name"] = $request->conditions[$i]["field_name"];
                    $conditions[$i]["operator"] = $request->conditions[$i]["operator"];
                    $conditions[$i]["value"] = $request->conditions[$i]["value"];
                }
            }
        }

        $orders = NULL;
        if (isset($request->orders) && is_array($request->orders)) {
            $orders = [];
            for ($i = 0; $i < count($request->orders); $i++) {
                if (isset($request->orders[$i]["field"])) {
                    $orders[$i]["field"] = $request->orders[$i]["field"];
                    if (isset($request->orders[$i]["type"])) {
                        $orders[$i]["type"] = $request->orders[$i]["type"];
                    } else {
                        $orders[$i]["type"] = "ASC";
                    }
                }
            }
        } else {
            //DEFAULT ORDER IF ANY
        }

        $where_in = NULL;
        if (isset($request->where_in) && is_array($request->where_in)) {
            if (isset($request->where_in["field_name"]) && isset($request->where_in["ids"]) && is_array($request->where_in["ids"])) {
                $where_in["field_name"] = $request->where_in["field_name"];
                $where_in["ids"] = $request->where_in["ids"];
            }
        }

        $data = std_get([
            "select" => $select_fields,
            "table_name" => "TRORD",
            "special_where" => $conditions,
            "where_in" => $where_in,
            "order_by" => $orders,
            "limit" => $limit,
            "offset" => $offset,
            "first_row" => $first_row
        ]);

        return response()->json($data, 200);
    }
}
