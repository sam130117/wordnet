<?php

class Wordnet
{
    private $db_config = [ 'host' => 'localhost', 'username' => 'root', 'password' => '', 'db' => 'wordnet' ];

    public function init(){
        try {
            $connection = new PDO("mysql:host=" . $this->db_config['host'] . "dbname=" . $this->db_config['db'],
                $this->db_config['username'],$this->db_config['password']);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'Connected to Database<br/>';

            $connection->prepare("SELECT DISTINCT lemma FROM MORPHOLOGY WHERE morph = :word");
            $connection->bindParam(':word', $word);

            foreach ($connection->query($sql) as $row)
            {
                echo $row["collection_brand"] ." - ". $row["collection_year"] ."<br/>";
            }


            $connection = null;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

}