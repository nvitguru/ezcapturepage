<?php

Class Database{

    private $server = "mysql:host=127.0.0.1;port=3306;dbname=ocialpo1_captureSystem";
    private $username = "ocialpo1_captureAdmin";
    private $password = "?aASs&7Zre=g=erZ7&sSAa?";
    private $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
    protected static $conn;

    public function open(){
        if (null === self::$conn) {
            try {
                self::$conn = new PDO($this->server, $this->username, $this->password, $this->options);
            } catch (PDOException $e) {
                echo "There is some problem in connection: " . $e->getMessage();
            }
        }
        return self:: $conn;
    }

    public function close(){
        self::$conn = null;
    }
}

$pdo = new Database();
