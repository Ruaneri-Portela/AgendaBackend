<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/parser.php";
$_POST["json"] = '{"user":"master","password":"90908585"}';
function genToken($size)
{
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-=+;:,.?";
    $token = "";
    for ($i = 0; $i < $size; $i++) {
        $token .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $token;
}
function login()
{
    if (!isset($_POST["json"])) {
        return analyzeError(0);
    }
    $data = json_decode($_POST["json"], true);
    if (!isset($data["user"]) || !isset($data["password"])) {
        return analyzeError(1);
    }
    $db = new database();
    $user = $db->checkUserLogin($data["user"], $data["password"]);
    if ($user > 0) {
        $token = genToken(64);
        $db->registerUserToken($token, $user, false);
        return processSucefull(5, $token, "token");
    }
    return analyzeError(14);
}
echo login();
