<?php
include "assets/db.php";
include "assets/entry.php";
include "assets/scheduling.php";
include "assets/parser.php";
function create()
{
    $result = entry(false, true, true, true, true, true, true);
    if (is_numeric($result))
        return analyzeError($result);
    $sh = new scheduling($result);
    return $sh->save();
}
echo create();
