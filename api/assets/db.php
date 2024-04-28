<?php
class database
{
    private $host = "localhost";
    private $dbName = "agendamento";
    private $username = "agendamento";
    private $password = "40405050";
    private $scheduleTable = "agendamentos";
    private $usersTable = "users";
    private $tokensTable = "tokens";
    public $conn;
    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbName);
        if ($this->conn->connect_error)
            die("Connection failed: " . $this->conn->connect_error);
    }
    public function insertScheduling($sh)
    {
        $query = "INSERT INTO " . $this->scheduleTable . " (room, timestampStart, timestampEnd, userId, reason) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            if (!isset($data["reason"]))
                $data["reason"] = "";
            $stmt->bind_param("issis", $sh->room, $sh->timestampStart, $sh->timestampEnd, $sh->userId, $sh->reason);
            $stmt->execute();
            $stmt->close();
            return $this->conn->insert_id;
        } else
            return -1;
    }
    public function detectSchedulingConflict($sh)
    {
        $query = "SELECT * FROM " . $this->scheduleTable . " WHERE room = ? AND ((timestampStart >= ? AND timestampStart <= ?) OR (timestampEnd >= ? AND timestampEnd <= ?))";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("issss", $sh->room, $sh->timestampStart, $sh->timestampEnd, $sh->timestampStart, $sh->timestampEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedulesAffected["amount"] = $result->num_rows;
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    $schedulesAffected[] = $row["id"];
                }
            }
            $stmt->close();
            return $schedulesAffected;
        } else
            return -1;
    }

    public function searchScheduling($room = "*", $timestampStart = "*", $timestampEnd = "*", $userId = "*")
    {
        if ($room === "*" && $timestampStart === "*" && $timestampEnd === "*" && $userId === "*") {
            return -1;
        }
        $query = "SELECT * FROM " . $this->scheduleTable . " WHERE";
        $notfist = false;
        $cmd = "";
        $reff = array();
        if ($room !== "*") {
            if ($notfist) {
                $query .= " AND";
            }
            $query .= " room = ?";
            $cmd .= "i";
            $notfist = true;
            $reff[] = &$room;
        }
        if ($timestampStart !== "*") {
            if ($notfist)
                $query .= " AND";
            $query .= " timestampStart >= ?";
            $cmd .= "s";
            $notfist = true;
            $reff[] = &$timestampStart;
        }
        if ($timestampEnd !== "*") {
            if ($notfist)
                $query .= " AND";

            $query .= " timestampEnd <= ?";
            $cmd .= "s";
            $notfist = true;
            $reff[] = &$timestampEnd;
        }
        if ($userId !== "*") {
            if ($notfist)
                $query .= " AND";

            $query .= " userId = ?";
            $cmd .= "i";
            $notfist = true;
            $reff[] = &$userId;
        }
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($cmd, ...$reff);
            $stmt->execute();
            $result = $stmt->get_result();
            $searchResult["amount"] = $result->num_rows;
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    $searchResult[] = $row["id"];
                }
            }
            $stmt->close();
            return $searchResult;
        } else
            return -3;
    }


    public function getScheduling($id)
    {
        $query = "SELECT * FROM " . $this->scheduleTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        } else
            return -1;
    }

    public function updateScheduling($sh)
    {
        $query = "UPDATE " . $this->scheduleTable . " SET room = ?, timestampStart = ?, timestampEnd = ?, userId = ?, reason = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            if (!isset($data["reason"]))
                $data["reason"] = "";
            $stmt->bind_param("issisi", $sh->room, $sh->timestampStart, $sh->timestampEnd, $sh->userId, $sh->reason, $sh->id);
            $stmt->execute();
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        } else
            return -1;
    }

    public function deleteScheduling($id)
    {
        $query = "DELETE FROM " . $this->scheduleTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        } else
            return -1;
    }

    public function checkUserLogin($user, $password)
    {
        $query = "SELECT * FROM " . $this->usersTable . " WHERE name = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $user = $result->fetch_assoc();
            if ($user) {
                if ($user["isPlain"] && $user["password"] == $password) {
                    return $user["id"];
                } else if (!$user["isPlain"] && password_verify($password, $user["password"])) {
                    return $user["id"];
                } else {
                    return -4;
                }
                return -3;
            } else
                return -2;
        } else
            return -1;
    }

    public function queryUser($id)
    {
        $query = "SELECT * FROM " . $this->usersTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $user = $result->fetch_assoc();
            return $user;
        } else
            return -1;
    }

    public function registerUserToken($token, $userId, $isApi)
    {
        if (strlen($token) != 64)
            return -2;
        $query = "INSERT INTO " . $this->tokensTable . "(token, userId, isApi) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("sii", $token, $userId, $isApi);
            $stmt->execute();
            $stmt->close();
            return $this->conn->insert_id;
        } else
            return -1;
    }

    public function unregisterUserToken($token){
        if (strlen($token) != 64)
            return -2;
        $query = "DELETE FROM " . $this->tokensTable . " WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->affected_rows;
            $stmt->close();
            return $result;
        } else
            return -1;
    }

    public function consultUserToken($token)
    {
        if (strlen($token) != 64)
            return -2;
        $query = "SELECT * FROM " . $this->tokensTable . " WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        } else
            return -1;
    }
}
