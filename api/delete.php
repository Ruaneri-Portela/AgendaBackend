<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/parser.php";
include "assets/scheduling.php";
function delete()
{
    $data = entry(true);
    if (is_numeric($data))
        return analyzeError($data);
    $db = new database();
    $result = $db->deleteScheduling($data["id"]);
    if ($result == -1)
        return analyzeError(6);
    else
        return analyzeError(7);
    $sc = new scheduling($result);
    return $sc->delete();
}
echo delete();
