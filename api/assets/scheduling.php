<?php
class scheduling
{
    public $id;
    public $room;
    public $timestampStart;
    public $timestampEnd;
    public $userId;
    public $reason;
    public function __construct($data)
    {
        $this->room = $data["room"];
        $this->timestampStart = $data["timestampStart"];
        $this->timestampEnd = $data["timestampEnd"];
        $this->userId = $data["userId"];
        if (isset($data["id"]))
            $this->id = $data["id"];
        else
            $this->id = -1;
        if (isset($data["reason"]))
            $this->reason = $data["reason"];
        else
            $this->reason = "";
    }
    public function save()
    {
        $db = new database();
        $result = $db->detectSchedulingConflict($this);
        if ($result == -1) {
            return analyzeError(4);
        } else {
            if ($result["amount"] > 0) {
                if ($this->id != -1) {
                    $hold = 0;
                    foreach ($result as $field) {
                        if ($field != $this->id && $hold == 1)
                            return analyzeError(3, $result, "conflicts");
                        $hold = 1;
                    }
                } else
                    return analyzeError(3, $result, "conflicts");
            }
        }
        if ($this->id != -1) {
            $result = $db->updateScheduling($this);
            switch ($result) {
                case -1:
                    return analyzeError(6);
                case 0:
                    return analyzeError(11);
                case -2:
                    return analyzeError(5);
                default:
                    return processSucefull(4);
            }
        } else {
            $result = $db->insertScheduling($this);
            if ($result == -1)
                return analyzeError(2);
            else
                return processSucefull(0);
        }
    }

    public function delete()
    {
        $db = new database();
        $result = $db->deleteScheduling($this->id);
        if ($result > 0)
            return processSucefull(3);
        elseif ($result == -1)
            return analyzeError(6);
        else
            return analyzeError(7);
    }
}
