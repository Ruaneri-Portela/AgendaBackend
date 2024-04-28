<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/parser.php";

function logout()
{
    if (!isset($_POST["json"])) {
        return analyzeError(0);
    }
    $data = json_decode($_POST["json"], true);
    if (!isset($data["token"])) {
        return analyzeError(1);
    }
    $db = new database();
    $result = $db->unregisterUserToken($data["token"]);
    return processSucefull(6, $result);
}
echo logout();
