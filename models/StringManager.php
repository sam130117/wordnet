<?php

namespace models;

use PDO;
use PDOException;

class StringManager
{
    private $db_config = ['host' => 'localhost', 'db' => 'wordnet', 'username' => 'root', 'password' => ''];

    static $partsOfSpeech = array('I', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'mine', 'yours',
        'his', 'hers', 'its', 'ours', 'theirs', 'myself', 'yourself', 'himself', 'herself', 'itself', 'ourselves',
        'yourselves', 'themselves', 'who', 'whom', 'what', 'which', 'whether', 'that', 'whoever', 'whomever', 'whose', 'why', 'when',
        'this', 'these', 'that', 'those',

        'a', 'an', 'the',

        'on', 'in', 'at', 'since', 'for', 'ago', 'during', 'before', 'after', 'until', 'till', 'to',
        'past', 'from', 'by', 'off', 'beside', 'under', 'over', 'below', 'above', 'up', 'down', 'across', 'through',
        'into', 'out of', 'onto', 'towards', 'of', 'about', 'with', 'without', 'throughout', 'against', 'along', 'among', 'around',
        'behind',

        'and', 'also', 'nor', 'or', 'else', 'but', 'whereas', 'while', 'yet', 'even', 'though', 'although',
        'just', 'as', 'both', 'neither', 'because', 'so', 'if', 'then', 'once', 'here', 'there',
        'absolutely', 'achoo', 'ack', 'ahh', 'aha', 'ahem', 'ahoy', 'agreed', 'alas', 'alright', 'not',

        'alrighty', 'alack', 'anytime', 'argh', 'anyhoo', 'how', 'anyhow', 'attaboy', 'attagirl', 'aww', 'awful', 'bam', 'bah',
        'humbug', 'behold', 'bingo', 'blah', 'boo', 'bravo', 'cheers', 'crud', 'darn', 'dang', 'doh', 'drat', 'duh',
        'eek', 'eh', 'gee', 'geepers', 'whiz', 'golly', 'goodness', 'gosh', 'ha', 'hallelujah', 'hey', 'hi', 'hmmm',
        'huh', 'indeed', 'no', 'nah', 'oops', 'ouch', 'phew', 'shucks', 'tut', 'uggh', 'woah', 'woops', 'wow',
        'yay', 'yikes',

        'am', 'are', 'is', 'was', 'were', 'been', 'has', 'have', 'had');



    public static function process($string)
    {
        $array = preg_split("/[,|.|;|:|!|?|!| |-|.|:|;|?|(|)|{|}|’|‘|\n|\r|]/", $string, -1, PREG_SPLIT_NO_EMPTY);
        $isFound = false;
        $db = new PDO("mysql:host=localhost;dbname=wordnet;", 'root', '');

        if ($array) {
            for ($i = 0; $i < count($array); $i++) {
                $array[$i] = lcfirst($array[$i]);
//                echo '-----------------------------<br/>';
                if (in_array($array[$i], self::$partsOfSpeech)) {
                    //if word is in array -> it is correct
                    $array[$i] = '-' . $array[$i] . '-';
//                    echo $array[$i] . ' word is in array -> correct<br/>';
                } else {
                    foreach (self::$partsOfSpeech as $partsOfSpeech) {
                        //find levenshtein distance among $partsOfSpeech
                        //than find levenshtein distance among wordnet
                        $distance = levenshtein($array[$i], $partsOfSpeech, 1, 1, 1);
                        if (strlen($array[$i]) && $distance == 1) {

                            try {
                                $stmt = $db->query("SELECT lemma FROM words WHERE lemma LIKE '" . $array[$i] . "'");
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if($result && ($result[0]['lemma']))
                                {
                                    $array[$i] = '-' . $array[$i] . '-';
                                    $isFound = true;
//                                    echo $array[$i] . ' word with distance 1 in array -> found db -> replaced<br/>';
                                }
                                else {
                                    //get closest word from $partsOfSpeech
//                                    echo 'Distance for ' . $array[$i] . ' and ' . $partsOfSpeech . ' = ' . $distance . "<br/>";
                                    $array[$i] = '/' . $partsOfSpeech . '/';
                                    $isFound = true;
//                                    echo $array[$i] . ' word with distance 1 in array -> not found in db, found closest in array -> replaced<br/>';
                                }
                            } catch (PDOException $e) {
                                return $e;
                            }
                        }
                    }

                    if(!$isFound)   // if the word was not found in $partsOfSpeech -> check other words in db
                    {
//                        echo $array[$i] . ' word was not found in array -> search in db<br/>';
                        $similarWords = [];
                        $length = strlen($array[$i]);

                        $globalStmt = $db->query("SELECT lemma FROM words WHERE wordGroup = " . (int)($length) .
                            " OR wordGroup = " . (int)($length + 1) . " OR wordGroup = " . (int)($length - 1));
                        $globalResult = $globalStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($globalResult as $item)
                        {
                            if($item['lemma'] != null)
                            {
                                if($array[$i] == $item['lemma'])
                                {
                                    $array[$i] = '-' . $array[$i] . '-';
//                                    echo $array[$i] . ' word from lemma found in db -> correct<br/>';
                                }
                                else if(self::endsWith($array[$i], 's') ) {
                                    if(substr($array[$i],0, strlen($array[$i]) - 1))
                                    {
                                        $array[$i] = '-' . $array[$i] . '-';
                                    }
                                }
                                else if(self::endsWith($array[$i], 'ed') ) {
                                    if(substr($array[$i],0, strlen($array[$i]) - 2))
                                    {
                                        $array[$i] = '-' . $array[$i] . '-';
                                    }
                                }
                                else if(self::endsWith($array[$i], 'ing') ) {
                                    if(substr($array[$i],0, strlen($array[$i]) - 2))
                                    {
                                        $array[$i] = '-' . $array[$i] . '-'; //TODO: add ing rules
                                    }
                                }
                                else
                                {
                                    $distance = levenshtein($array[$i], $item['lemma'], 1, 1, 1);
                                    if($distance == 1)
                                    {
//                                        echo 'Distance for ' . $array[$i] . ' and ' . $item['lemma'] . ' = ' . $distance . "<br/>";
                                        array_push($similarWords, $item['lemma']);
                                    }
                                }
                            }
                        }
                        if (strpos($array[$i], '-') === false) {    //word was not found -> get closest from db
                            if($similarWords)
                                $array[$i] = '/' . $similarWords[0] . '/';
                        }
//                        var_dump($similarWords);
                    }
                    $isFound = false;
                }
//                echo '-----------------------------<br/>';
            }
        } else {
            $array = $string;
        }
        return $array;
    }

    private static function insertMany()
    {
        $db = new PDO('mysql:host=localhost;dbname=wordnet;', 'root', '');
        $file = fopen('C:\Users\sam13\Desktop\ttt.txt', "r") or die("Unable to open file!");
        $result = [];
        while(!feof($file)) {
            $result[] = trim(fgets($file));
        }
        fclose($file);
        foreach ($result as $item)
            $db->exec("INSERT INTO words (`lemma`) VALUES ('$item')");
    }

    private static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }
}