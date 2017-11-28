<?php

namespace models;


use PDO;
use PDOException;

class StringManager
{
    static $partsOfSpeech = array('I', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'mine', 'yours',
        'his', 'hers', 'its', 'ours', 'theirs', 'myself', 'yourself', 'himself', 'herself', 'itself', 'ourselves',
        'yourselves', 'themselves', 'who', 'whom', 'what', 'which', 'that', 'whoever', 'whomever', 'whose', 'why', 'when',
        'this', 'these', 'that', 'those',

        'a', 'an', 'the',

        'on', 'in', 'at', 'since', 'for', 'ago', 'during', 'before', 'after', 'until', 'till', 'to',
        'past', 'from', 'by', 'off', 'beside', 'under', 'over', 'below', 'above', 'up', 'down', 'across', 'through',
        'into', 'out of', 'onto', 'towards', 'of', 'about', 'with', 'throughout', 'against', 'along', 'among', 'around',
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
        $db = new PDO('mysql:host=localhost;dbname=wordnet;', 'root', '');


        if ($array) {
            for ($i = 0; $i < count($array) - 1; $i++) {
                echo '-----------------------------<br/>';
                if (in_array($array[$i], self::$partsOfSpeech)) {
                    //if word is in array -> it is correct
                    $array[$i] = '-' . $array[$i] . '-';
                    echo $array[$i] . ' word is in array -> correct<br/>';
                } else {
                    foreach (self::$partsOfSpeech as $partsOfSpeech) {
                        //find levenshtein distance among $partsOfSpeech
                        //than find levenshtein distance among wordnet
                        //choose
                        $distance = levenshtein($array[$i], $partsOfSpeech, 1, 1, 1);
                        if (strlen($array[$i]) && $distance == 1) {

                            try {
                                $stmt = $db->query("SELECT lemma, morph, pos, morphmaps.morphid FROM words " .
                                    "LEFT JOIN morphmaps ON morphmaps.wordid = words.wordid LEFT JOIN morphs ON " .
                                    "morphs.morphid = morphmaps.morphid WHERE lemma LIKE '" . $array[$i] .
                                    "' OR morph LIKE '" . $array[$i] ."'");
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if($result && ($result[0]['lemma'] || $result[0]['morph']))
                                {
                                    $array[$i] = '-' . $array[$i] . '-';
                                    $isFound = true;
                                    echo $array[$i] . ' word with distance 1 in array -> found db -> replaced<br/>';
                                }
                                else {
                                    //get closest word from $partsOfSpeech
//                                    echo 'Distance for ' . $array[$i] . ' and ' . $partsOfSpeech . ' = ' . $distance . "<br/>";
                                    $array[$i] = '/' . $partsOfSpeech . '/';
                                    $isFound = true;
                                    echo $array[$i] . ' word with distance 1 in array -> not found in db, found closest in array -> replaced<br/>';
                                }
                            } catch (PDOException $e) {
                                return $e;
                            }
                        }
                    }

                    echo $array[$i] . '<br/>';

                    if(!$isFound)   // if the word was not found in $partsOfSpeech -> check other words in db
                    {
                        echo '+';
                        echo $array[$i] . ' word was not found in array -> search in db<br/>';
                        $similarWords = [];

                        $length = strlen($array[$i]);

                        $globalStmt = $db->query("SELECT lemma, morph FROM words " .
                            "LEFT JOIN morphmaps ON morphmaps.wordid = words.wordid LEFT JOIN morphs ON " .
                            "morphs.morphid = morphmaps.morphid WHERE LENGTH(lemma) = " . (int)($length) . " OR LENGTH(lemma) = " . (int)($length + 1) .
                        " OR LENGTH(lemma) = " . (int)($length - 1) . " OR LENGTH(morph) = " . (int)($length) . " OR LENGTH(morph) = " . (int)($length + 1) .
                        " OR LENGTH(morph) = " . (int)($length - 1));
                        $globalResult = $globalStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($globalResult as $item)
                        {
                            if($item['lemma'] != null)
                            {
                                if($array[$i] == $item['lemma'])
                                {
                                    $array[$i] = '-' . $array[$i] . '-';
                                    echo $array[$i] . ' word from lemma found in db -> correct<br/>';
                                }
                                else
                                {
                                    $distance = levenshtein($array[$i], $item['lemma'], 1, 1, 1);
                                    if($distance == 1)
                                    {
                                        echo 'Distance for ' . $array[$i] . ' and ' . $item['lemma'] . ' = ' . $distance . "<br/>";
                                        $similarWords[] = $item['lemma'];
                                        $array[$i] = '/' . $item['lemma'] . '/';
                                    }
                                }
                            }
                            else if($item['morph'] != null)
                            {
                                $distance = levenshtein($array[$i], $item['morph'], 1, 1, 1);
                                if($array[$i] == $item['morph'])
                                {
                                    $array[$i] = '-' . $array[$i] . '-';
                                    echo $array[$i] . ' word from morph found in db -> correct<br/>';
                                }
                                else
                                {
                                    if($distance == 1)
                                    {
                                        echo 'Distance for ' . $array[$i] . ' and ' . $item['morph'] . ' = ' . $distance . "<br/>";
                                        $similarWords[] = $item['morph'];
                                        $array[$i] = '/' . $item['morph'] . '/';
                                    }
                                }
                            }
                        }
                        var_dump($similarWords);
                    }
                    $isFound = false;
                }
                echo '-----------------------------<br/>';
            }
        } else {
            $array = $string;
        }
        return $array;
    }
}