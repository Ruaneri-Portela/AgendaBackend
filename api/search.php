<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/scheduling.php";
include "assets/parser.php";
function search()
{
    $data = entry(false, false, false, false, false, false, false);
    if (is_numeric($data)) {
        return analyzeError($data);
    }
    $db = new database();
    $result = $db->searchScheduling($data["room"], $data["timestampStart"], $data["timestampEnd"], $data["userId"]);
    switch ($result) {
        case -1:
            return analyzeError(9);
        case -2:
            return analyzeError(5);
        case -3:
            return analyzeError(8);
    }
    $count = 0;
    if ($result["amount"] == 0) {
        return analyzeError(12);
    }
    foreach ($result as $row) {
        if ($count > 0) {
            $data = $db->getScheduling($row);
            $sh[] = new scheduling($data);
        }
        $count++;
    }
    if (isset($sh)) {
        return processSucefull(1, array("amount" => $count - 1, "result" => $sh), "list");
    }
}
echo search();
