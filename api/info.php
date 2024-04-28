<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/parser.php";
function info()
{
    $data = array(
        "version" => "0.1",
        "name" => "Agendamento API",
        "envoriment" => $_ENV['HTTP_HOST'],
        "os" => $_ENV['OS'],
        "server" => $_ENV['SERVER_SOFTWARE']
    );
    if (isset($_POST["json"])) {
        $jsondata = json_decode($_POST["json"], true);
        if (isset($jsondata["token"])) {
            $db = new database;
            $tokenInfo = $db->consultUserToken($jsondata["token"]);
            if ($tokenInfo == null) {
                array_push($data, array("loginStatus" => 0));
                return $data;
            }
            $userInfo = $db->queryUser($tokenInfo["userId"]);
            if ($userInfo == null) {
                array_push($data, array("loginStatus" => 0));
                return $data;
            }
            array_push($data, array("loginStatus" => 1, "username" => $userInfo["name"], "email" => $userInfo["email"]));
            return $data;
        }
    }
    return $data;
}
echo processSucefull(2, info(), "info");
