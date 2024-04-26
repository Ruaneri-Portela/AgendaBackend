<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/scheduling.php";
include "assets/parser.php";
function edit()
{
    $data = entry(true, true, true, true, true, true, true);
    if (is_numeric($data)) {
        return analyzeError($data);
    }
    $db = new database();
    $sh = new scheduling($db->getScheduling($data["id"]));
    if (is_numeric($sh) && $sh == -1) {
        return analyzeError(8);
    }
    isset($data["room"]) ? $sh->room = $data["room"] : $sh->room;
    isset($data["timestampStart"]) ? $sh->timestampStart = $data["timestampStart"] : $sh->timestampStart;
    isset($data["timestampEnd"]) ? $sh->timestampEnd = $data["timestampEnd"] : $sh->timestampEnd;
    isset($data["userId"]) ? $sh->userId = $data["userId"] : $sh->userId;
    isset($data["reason"]) ? $sh->reason = $data["reason"] : $sh->reason;
    return $sh->save();
}
echo edit();
