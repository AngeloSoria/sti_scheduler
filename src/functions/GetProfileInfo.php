<?php

class GetProfileInfo
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getProfileInfo($userId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE UserID = :userid");
        $stmt->bindParam(":userid", $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result > 0) {
            return $result;
        } else {
            return null;
        }
    }
}