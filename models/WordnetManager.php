<?php

namespace models;

use PDO;
use PDOException;

class WordnetManager
{
    public static function addWord($lemma, $frequency = 0, $sample = '')
    {
        $db = new PDO("mysql:host=localhost;dbname=wordnet;", 'root', '');
        $stmt = $db->query("SELECT * FROM `words` WHERE `lemma` LIKE '$lemma'");
        if ($stmt->rowCount())
        {
            //check if word is not in freq table
            if(self::frequencyAdd($db, $frequency, $lemma))
                return 'Lemma was added to frequency dictionary.';
            else return 'Lemma already exists!';
        }

        $group = strlen($lemma);
        $stmt = $db->query("INSERT INTO `words` (`lemma`, `wordGroup`, `sample`) VALUES ('$lemma', '$group', '$sample')");
        if($stmt)
        {
            if(self::frequencyAdd($db, $frequency, $lemma))
                return 'Lemma was added to words and frequency dictionary.';
            else return 'Lemma was added to words dictionary.';
        }
        return false;
    }

    public static function frequencyAdd($db, $frequency, $lemma){
        if($frequency == 0) return false;

        $stmt = $db->query("SELECT * FROM `word-frequency` WHERE `lemma` LIKE '$lemma'");
        if (!$stmt->rowCount()) {
            return $db->query("INSERT INTO `word-frequency` (`lemma`, `frequency`) VALUES ('$lemma', '$frequency')");
        }
        return false;
    }

}