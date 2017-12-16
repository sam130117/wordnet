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

        'and', 'shall', 'also', 'nor', 'or', 'else', 'but', 'whereas', 'while', 'yet', 'even', 'though', 'although',
        'just', 'as', 'both', 'neither', 'because', 'so', 'if', 'then', 'once', 'here', 'there',
        'absolutely', 'achoo', 'ack', 'ahh', 'aha', 'ahem', 'ahoy', 'agreed', 'alas', 'alright', 'not',

        'alrighty', 'alack', 'anytime', 'argh', 'anyhoo', 'how', 'anyhow', 'attaboy', 'attagirl', 'aww', 'awful', 'bam', 'bah',
        'humbug', 'behold', 'bingo', 'blah', 'boo', 'bravo', 'cheers', 'crud', 'darn', 'dang', 'doh', 'drat', 'duh',
        'eek', 'eh', 'gee', 'geepers', 'whiz', 'golly', 'goodness', 'gosh', 'ha', 'hallelujah', 'hey', 'hi', 'hmmm',
        'huh', 'indeed', 'no', 'nah', 'oops', 'ouch', 'phew', 'shucks', 'tut', 'uggh', 'woah', 'woops', 'wow',
        'yay', 'yikes', 'upon',

        'am', 'are', 'is', 'was', 'were', 'been', 'has', 'have', 'had');


    public static function process($string)
    {
//        $array = preg_split("/[,|.|—|;|!’|?’|:|!|?|.)|!| |.|:|;|’,|?|(|)|{|}|‘|\n|\r|]/", $string, -1, PREG_SPLIT_NO_EMPTY);
        $array = preg_split("/([^ \n\r]+[ \n\r]+)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $db = new PDO("mysql:host=localhost;dbname=wordnet;", 'root', '');
        $array = array_map('trim', $array);

        if ($array) {
            for ($i = 0; $i < count($array); $i++) {
                $initialWord = $array[$i];              //save initial word
                $lettersArray = str_split($array[$i]);
                $newWord = '';
                for ($k = 0; $k < count($lettersArray); $k++) {
                    //remove signs
                    if ($lettersArray[$k] == '.' || $lettersArray[$k] == ',' || $lettersArray[$k] == '—' ||
                        $lettersArray[$k] == ';' || $lettersArray[$k] == '!' || $lettersArray[$k] == '?' ||
                        $lettersArray[$k] == ':' || $lettersArray[$k] == '(' || $lettersArray[$k] == ')' ||
                        $lettersArray[$k] == '{' || $lettersArray[$k] == '}' || $lettersArray[$k] == '"' ||
                        $lettersArray[$k] == '\''
                    ) {
                        if ($lettersArray[$k] == '\'') {
                            if (($k + 1 < count($lettersArray)) && ($k + 2 < count($lettersArray)) && ($lettersArray[$k + 1] == 'l' && $lettersArray[$k + 2] == 'l'
                                    || $lettersArray[$k + 1] == 'v' && $lettersArray[$k + 2] == 'e')
                            ) {
                                $newWord .= $lettersArray[$k];
                                continue;
                            } else if (($k + 1 < count($lettersArray)) && $lettersArray[$k + 1] == 't') {
                                $newWord .= $lettersArray[$k];
                                continue;
                            } else continue;
                        } else continue;
                    }
                    $newWord .= $lettersArray[$k];
                }
//                echo 'initial: ' . $initialWord;
//                echo '<br/>';
//                echo 'new:' . $newWord;
//                echo '<br/>';

                $array[$i] = strtolower($newWord);

                if (in_array($array[$i], self::$partsOfSpeech)) {               //search word in array, if found => it is correct
                    $array[$i] = '-' . $array[$i] . '-';
                } else {                                                        //search word in db, if found => it is correct
                    if (strpos($array[$i], '\'ll') !== false || strpos($array[$i], '\'ve') !== false
                        || strpos($array[$i], 'n\'t') !== false
                    ) {
                        $array[$i] = '-' . $array[$i] . '-';
                    }
                    if (self::endsWith($array[$i], 's')) {
                        if (substr($array[$i], 0, strlen($array[$i]) - 1)) {
                            $array[$i] = substr($array[$i], 0, strlen($array[$i]) - 1);
//                            var_dump($array[$i]);
                        }
                    }
                    $stmt = $db->query("SELECT lemma FROM words WHERE lemma LIKE '" . $array[$i] .
                        "' OR sample LIKE '% " . $array[$i] . " %' LIMIT 1");
                    if (!$stmt)
                        continue;
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result && ($result[0]['lemma'])) {
                        $array[$i] = '-' . $array[$i] . '-';
                    } else {
                        //find all words from array and from db, where distance = 1
                        $similarWords = [];
                        foreach (self::$partsOfSpeech as $word) {
                            $distance = levenshtein($array[$i], $word, 1, 1, 1);
                            if ($distance == 1) {
//                                echo 'Distance array for ' . $array[$i] . ' and ' . $word . ' = ' . $distance . "<br/>";
                                array_push($similarWords, $word);
                            }
                        }

                        $length = strlen($array[$i]);
                        $in = " $length, $length + 1, $length - 1 ";
                        $partsQuery = '';

                        for ($j = 0; $j < strlen($array[$i]) - 2; $j++) {
                            if (strlen($array[$i]) == 3) {
                                $partsQuery = '';
                                break;
                            }
                            $part = $array[$i][$j] . $array[$i][$j + 1] . $array[$i][$j + 2];
                            if ($j == strlen($array[$i]) - 3)
                                $partsQuery .= " lemma LIKE '%$part%' )";
                            else
                                $partsQuery .= " AND ( lemma LIKE '%$part%' OR ";
                        }
                        $sql = "SELECT lemma FROM words WHERE wordGroup IN ($in) ";
//                        var_dump($sql);
                        $globalStmt = $db->query($sql);
                        if (!$globalStmt)
                            continue;

                        $dbWords = $globalStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($dbWords as $dbWord) {
                            if ($dbWord['lemma'] != null) {
                                $distance = levenshtein($array[$i], $dbWord['lemma'], 1, 1, 1);
                                if ($distance == 1) {
//                                    echo 'Distance db for ' . $array[$i] . ' and ' . $dbWord['lemma'] . ' = ' . $distance . "<br/>";
                                    array_push($similarWords, $dbWord['lemma']);
                                }
                            }
                        }
                        //find which word has max frequency and suggest it
                        //if there is no such word => suggest any
//                        var_dump($similarWords);
                        $maxFrequency = -1;
                        $suggestedWord = '';
                        foreach ($similarWords as $word) {
                            $sql = "SELECT frequency FROM `word-frequency` WHERE lemma LIKE '$word' LIMIT 1";
                            $globalStmt = $db->query($sql);
                            if (!$globalStmt)
                                continue;
                            $dbWord = $globalStmt->fetchAll(PDO::FETCH_ASSOC);
                            if ($dbWord) {
                                if ($dbWord[0]['frequency'] > $maxFrequency) {
                                    $maxFrequency = $dbWord[0]['frequency'];
                                    $suggestedWord = $word;
                                }
                            }
                        }
//                        var_dump($suggestedWord . ' : ' . $maxFrequency);
                        if ($suggestedWord != '') {
                            $array[$i] = '/' . $suggestedWord . '/';
                        } else {
                            if (!empty($similarWords))
                                $array[$i] = '/' . $similarWords[0] . '/';
                        }
                    }
                }
                if ($initialWord != $array[$i]) {
                    $newWordReplaced = '';
                    $r = str_replace('-', '', $array[$i]);
                    $r = str_replace('/','',$r);
                    var_dump($r);
                    $t = str_split($r);
                    for ($g = 0; $g < count($t); $g++) {
                        if ($t[$g] == '.' || $t[$g] == ',' || $t[$g] == '—' ||
                            $t[$g] == ';' || $t[$g] == '!' || $t[$g] == '?' ||
                            $t[$g] == ':' || $t[$g] == '(' || $t[$g] == ')' ||
                            $t[$g] == '{' || $t[$g] == '}' || $t[$g] == '"' ||
                            $t[$g] == '\'' || $t[$g] == '-'
                        ) {
                            $newWordReplaced .= $t[$g];
                        } else {
                            if (ctype_upper($initialWord[$g]) && strtoupper($t[$g]) == $initialWord[$g]) {
                                $newWordReplaced .= strtoupper($t[$g]);
                            }
                            else {
                                $newWordReplaced .= $t[$g];
                            }
                        }
                    }
                    var_dump($newWordReplaced);
                }
            }

        } else {
            $array = $string;
        }
//        print_r($array);
        return $array;
    }

    public
    static function insertMany()
    {
        $db = new PDO('mysql:host=localhost;dbname=wordnet;', 'root', '');
        $file = fopen('C:\Users\sam13\Desktop\ttt.txt', "r") or die("Unable to open file!");
        $words = [];
        while (!feof($file)) {
            $words[] = trim(fgets($file));
        }
        fclose($file);

        $file = fopen('C:\Users\sam13\Desktop\fff.txt', "r") or die("Unable to open file!");
        $fr = [];
        while (!feof($file)) {
            $fr[] = trim(fgets($file));
        }
        fclose($file);

        foreach ($words as $index => $word)
            $db->exec("INSERT INTO `word-frequency` (`lemma`, `frequency`) VALUES ('$words[$index]', '$fr[$index]')");
    }

    private
    static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

//$stmt = $db->query("select words.wordid, words.lemma, samples.sample from words inner join senses on
//words.wordid = senses.wordid inner join samples on samples.synsetid = senses.synsetid ORDER BY `words`.`wordid` ASC");
//        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//        foreach ($result as $item)
//        {
////            var_dump($item);
//            $stmt = $db->query("update words set `sample` = '" . $item['sample'] . "' where wordid = '" . $item['wordid'] . "'");
//        }
//        die();
}