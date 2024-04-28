<?php
function analyzeError($code = -1, $additional = "", $name = "additional")
{
    $data = array(
        "type" => "error",
        "code" => $code,
        "msg" => "",
        $name => $additional
    );
    switch ($code) {
        case 0:
            $data["msg"] = "Invalid POST";
            break;
        case 1:
            $data["msg"] = "Invalid JSON";
            break;
        case 2:
            $data["msg"] = "Error on insert scheduling";
            break;
        case 3:
            $data["msg"] = "Conflict detected";
            break;
        case 4:
            $data["msg"] = "Error on detect conflict";
            break;
        case 5:
            $data["msg"] = "Start time is higher or equal than end time";
            break;
        case 6:
            $data["msg"] = "Error on delete scheduling";
            break;
        case 7:
            $data["msg"] = "None scheduling deleted";
            break;
        case 8:
            $data["msg"] = "Error on search scheduling";
            break;
        case 9:
            $data["msg"] = "Is necessary at least one parameter to search";
            break;
        case 10:
            $data["msg"] = "Is necessary at least one parameter to update";
            break;
        case 11:
            $data["msg"] = "None scheduling edited";
            break;
        case 12:
            $data["msg"] = "None scheduling found";
            break;
        case 13:
            $data["msg"] = "This scheduling is more older than time present";
            break;
        case 14:
            $data["msg"] = "Login Error";
            break;
        case 15:
            $data["msg"] = "Token not found";
            break;
        default:
            $data["msg"] = "Unknown error";
            break;
    }
    return json_encode($data);
}
function processSucefull($code = -1, $additional = "", $name = "additional")
{
    $data = array(
        "type" => "success",
        "code" => $code,
        "msg" => "",
        $name => $additional
    );
    switch ($code) {
        case 0:
            $data["msg"] = "Create scheduling";
            break;
        case 1:
            $data["msg"] = "Search scheduling";
            break;
        case 2:
            $data["msg"] = "Info";
            break;
        case 3:
            $data["msg"] = "Deleted scheduling";
            break;
        case 4:
            $data["msg"] = "Updated scheduling";
            break;
        case 5:
            $data["msg"] = "Login sucessulf ";
            break;
        case 6:
            $data["msg"] = "Logout sucessulf";
            break;
        default:
            $data["msg"] = "Unknown";
            break;
    }
    return json_encode($data);
}
