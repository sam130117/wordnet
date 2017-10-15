<?php

namespace models;

use PDO;
use PDOException;

class Wordnet
{
    private $db_config = [ 'host' => 'localhost', 'username' => 'root', 'password' => '', 'db' => 'wordnet' ];

    public function __construct($word)
    {
        $this->run($word);
    }

    private function run($word){
        try {
            $connection = new PDO("mysql:dbname=" . $this->db_config['db'] . ';host=' . $this->db_config['host'],
                $this->db_config['username'], $this->db_config['password']);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//            $statement = $connection->prepare("SELECT DISTINCT lemma FROM MORPHOLOGY WHERE morph = :word");
//            $statement->execute([':word' => $word]);
            $statement = $connection->prepare("SELECT lemma, pos, definition, sampleset FROM DICT WHERE lemma = :word");
            $statement->execute([':word' => $word]);

            $result = [];
            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
//                $result['lemma'] = $row['lemma'];
//                $result['pos'] = $row['pos'];
//                $result['definition'] = $row['definition'];
//                $result['sampleset'] = $row['sampleset'];
                $result[] = $row;
            }
//            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $connection = null;
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
        return null;
    }

}