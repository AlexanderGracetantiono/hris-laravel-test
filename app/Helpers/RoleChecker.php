<?php

function check_is_role_allowed($roles)
{
    if (
            session("user_initial_name") != NULL && 
            session("user_id") != NULL && 
            session("user_code") != null &&
            session("user_name") != NULL && 
            session("user_role") != NULL && 
            session("company_code") != null &&
            session("company_name") != null &&
            session("brand_code") != null &&
            session("brand_name") != null
        ) {
        if ($roles != NULL) {
            $user = get_master_employee("*",[
                [
                    "field_name" => "MAEMP_ID",
                    "operator" => "=",
                    "value" => session("user_id")
                ]
            ],true);

            if ($user["MAEMP_IS_DELETED"] == 1 || $user["MAEMP_BLOCKED_STATUS"] == 1 || $user["MAEMP_STATUS"] != 1 || $user["MAEMP_ACTIVATION_STATUS"] != 1) {
                session()->flush();
                redirect("/");
            }

            $status = FALSE;
            foreach ($roles as $role) {
                if (session("user_role") == $role) {
                    $status = TRUE;
                }
            }
            if ($status === FALSE) {
                session()->flush();
                redirect("/");
            }
        }
    }
    else{
        session()->flush();
        redirect("/");
    }
}
