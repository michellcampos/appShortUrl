<?php

class databaseConnect
{

    public function __construct($host, $user, $pass, $name){
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
    }

    public function dbConnection()
    {
        try {
            $dbh = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    }

}

