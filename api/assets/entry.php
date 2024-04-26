<?php
date_default_timezone_set('America/Sao_Paulo');
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
function entry($id = false, $room = false, $timestampStart = false, $timestampEnd = false, $userId = false, $reason = false, $presentDependent = false)
{
    if (isset($_POST["json"])) {
        $data = json_decode($_POST["json"], true);
        if (!isset($data["token"])) {
            return 1;
        } else {
            $db = new database;
            $result = $db->consultUserToken($data["token"]);
            if ($result != null && $result["userId"] > 0) {
                $requiredFields = ["id", "room", "timestampStart", "timestampEnd", "userId", "reason"];
                foreach ($requiredFields as $field) {
                    if ($$field && !isset($data[$field])) {
                        return 1;
                    }
                }
                if (isset($data["timestampStart"]) && isset($data["timestampEnd"]) && ($data["timestampStart"] != "*"  && $data["timestampEnd"] != "*")) {
                    $timeMinor  = date("Y-m-d H:i:s", strtotime($data["timestampStart"]));
                    $timeMajor = date("Y-m-d H:i:s", strtotime($data["timestampEnd"]));
                    if ($timeMajor <= $timeMinor) {
                        return 5;
                    }
                    $now = date("Y-m-d H:i:s");
                    if ($presentDependent && !($timeMinor > $now) && !($timeMajor > $now)) {
                        return 13;
                    }
                }
                return $data;
            } else {
                return 15;
            }
        }
    } else {
        return 0;
    }
}
