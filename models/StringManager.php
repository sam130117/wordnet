<?php

namespace models;

use PDO;
use PDOException;

class StringManager
{
    static $partsOfSpeech = array('I', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'mine', 'yours',
        'his', 'hers', 'its', 'ours', 'theirs', 'myself', 'yourself', 'himself', 'herself', 'itself', 'ourselves',
        'yourselves', 'themselves', 'who', 'whom', 'what', 'which', 'whether', 'that', 'whoever', 'whomever', 'whose', 'why', 'when',
        'this', 'these', 'that', 'those', 'a', 'an', 'the', 'on', 'in', 'at', 'since', 'for', 'ago', 'during', 'before', 'after', 'until', 'till', 'to',
        'past', 'from', 'by', 'off', 'beside', 'under', 'over', 'below', 'above', 'up', 'down', 'across', 'through',
        'into', 'out of', 'onto', 'towards', 'of', 'about', 'with', 'without', 'throughout', 'against', 'along', 'among', 'around',
        'behind', 'and', 'shall', 'also', 'nor', 'or', 'else', 'but', 'whereas', 'while', 'yet', 'even', 'though', 'although',
        'just', 'as', 'both', 'neither', 'because', 'so', 'if', 'then', 'once', 'here', 'there', 'absolutely', 'achoo', 'ack', 'ahh', 'aha', 'ahem', 'ahoy', 'agreed', 'alas', 'alright', 'not',
        'alrighty', 'alack', 'anytime', 'argh', 'anyhoo', 'how', 'anyhow', 'attaboy', 'attagirl', 'aww', 'awful', 'bam', 'bah',
        'humbug', 'behold', 'bingo', 'blah', 'boo', 'bravo', 'cheers', 'crud', 'darn', 'dang', 'doh', 'drat', 'duh',
        'eek', 'eh', 'gee', 'geepers', 'whiz', 'golly', 'goodness', 'gosh', 'ha', 'hallelujah', 'hey', 'hi', 'hmmm',
        'huh', 'indeed', 'no', 'nah', 'oops', 'ouch', 'phew', 'shucks', 'tut', 'uggh', 'woah', 'woops', 'wow',
        'yay', 'yikes', 'upon', 'am', 'are', 'is', 'was', 'were', 'been', 'has', 'have', 'had');



    public static function parseInitialString($string)
    {
        $string = trim($string);
        $array = preg_split("/([^ \n\r]+[ \n\r]+)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $array = array_map('trim', $array);
        return $array;
    }

    public static function processWord($word)
    {
        $lettersArray = str_split($word);
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
        return $newWord;
    }

    public static function process($string)
    {
//        $string = trim($string);
//        $array = preg_split("/([^ \n\r]+[ \n\r]+)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $recognizedWords = [];
        $unknownWords = [];
        $fixedWords = [];
        $similarWordsResult = [];

        $db = new PDO("mysql:host=localhost;dbname=wordnet;", 'root', '');
//        $array = array_map('trim', $array);
        $array = self::parseInitialString($string);
        if ($array) {
            for ($i = 0; $i < count($array); $i++) {
                $initialWord = $array[$i];              //save initial word
                $newWord = self::processWord($array[$i]);
//                $lettersArray = str_split($array[$i]);
//                $newWord = '';
//                for ($k = 0; $k < count($lettersArray); $k++) {
//                    //remove signs
//                    if ($lettersArray[$k] == '.' || $lettersArray[$k] == ',' || $lettersArray[$k] == '—' ||
//                        $lettersArray[$k] == ';' || $lettersArray[$k] == '!' || $lettersArray[$k] == '?' ||
//                        $lettersArray[$k] == ':' || $lettersArray[$k] == '(' || $lettersArray[$k] == ')' ||
//                        $lettersArray[$k] == '{' || $lettersArray[$k] == '}' || $lettersArray[$k] == '"' ||
//                        $lettersArray[$k] == '\''
//                    ) {
//                        if ($lettersArray[$k] == '\'') {
//                            if (($k + 1 < count($lettersArray)) && ($k + 2 < count($lettersArray)) && ($lettersArray[$k + 1] == 'l' && $lettersArray[$k + 2] == 'l'
//                                    || $lettersArray[$k + 1] == 'v' && $lettersArray[$k + 2] == 'e')
//                            ) {
//                                $newWord .= $lettersArray[$k];
//                                continue;
//                            } else if (($k + 1 < count($lettersArray)) && $lettersArray[$k + 1] == 't') {
//                                $newWord .= $lettersArray[$k];
//                                continue;
//                            } else continue;
//                        } else continue;
//                    }
//                    $newWord .= $lettersArray[$k];
//                }
//                echo 'initial: ' . $initialWord;
//                echo '<br/>';
//                echo 'new:' . $newWord;
//                echo '<br/>';

                $array[$i] = strtolower($newWord);

                if (in_array($array[$i], self::$partsOfSpeech)) {               //search word in array, if found => it is correct
                    $array[$i] = '&' . $array[$i] . '&';
                    $similarWordsResult[$i]['word'] = $array[$i];
                } else {                                                        //search word in db, if found => it is correct
                    if (strpos($array[$i], '\'ll') !== false || strpos($array[$i], '\'ve') !== false
                        || strpos($array[$i], 'n\'t') !== false
                    ) {
                        $array[$i] = '&' . $array[$i] . '&';
                        $similarWordsResult[$i]['word'] = $array[$i];
                    }
                    if (self::endsWith($array[$i], 's')) {
                        if (substr($array[$i], 0, strlen($array[$i]) - 1)) {
                            $array[$i] = substr($array[$i], 0, strlen($array[$i]) - 1);
                        }
                    }
                    $stmt = $db->query("SELECT lemma FROM words WHERE lemma LIKE '" . $array[$i] .
                        "' OR sample LIKE '% " . $array[$i] . " %' LIMIT 1");
                    if (!$stmt)
                        continue;
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result && ($result[0]['lemma'])) {
                        $array[$i] = '&' . $array[$i] . '&';
                        $similarWordsResult[$i]['word'] = $array[$i];
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
                        $sql = "SELECT lemma FROM words WHERE wordGroup IN ($in) ";
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
                            else
                                $array[$i] = '#' . $array[$i] . '#';
                        }

                        if (count($similarWords) > 1) {
                            $j = 0;
                            $a = [];
//                            var_dump($similarWords);
                            foreach ($similarWords as $similarWord) {
                                if ($j == 2) break;
                                if ($similarWord == $suggestedWord) continue;

                                $a[] = $similarWord;
                                $j++;
                            }
                            $similarWordsResult[$i]['similar-words'] = $a;
                        }
                    }
                }

//                if ($initialWord != $array[$i]) { //always true
                    $newWordReplaced = '';
                    $wordStatus = '';

                    if ($array[$i][0] == '&' && $array[$i][strlen($array[$i]) - 1] == '&') {
                        $wordStatus = 'correct';
                        $resultString = trim($array[$i], "&");
                        $recognizedWords[] = $resultString;
                    } else if ($array[$i][0] == '/' && $array[$i][strlen($array[$i]) - 1] == '/') {
                        $wordStatus = 'error';
                        $resultString = trim($array[$i], "/");
                        $fixedWords[] = $resultString;
                    } else if ($array[$i][0] == '#' && $array[$i][strlen($array[$i]) - 1] == '#') {
                        $wordStatus = 'unknown';
                        $resultString = trim($array[$i], "#");
                        $unknownWords[] = $resultString;
                    }

//                    var_dump('Initial: ' . $initialWord);
//                    var_dump('Found replaced: ' . $resultString);
                    $foundWordArray = str_split($resultString);
                    $initialWordArray = str_split($initialWord);
                    $index = 0;
                    for ($g = 0; $g < count($initialWordArray); $g++) {
                        if ($initialWordArray[$g] == '.' || $initialWordArray[$g] == ',' || $initialWordArray[$g] == '—' ||
                            $initialWordArray[$g] == ';' || $initialWordArray[$g] == '!' || $initialWordArray[$g] == '?' ||
                            $initialWordArray[$g] == ':' || $initialWordArray[$g] == '(' || $initialWordArray[$g] == ')' ||
                            $initialWordArray[$g] == '{' || $initialWordArray[$g] == '}' || $initialWordArray[$g] == '"' ||
                            $initialWordArray[$g] == '\''
                        ) {
                            $newWordReplaced .= $initialWordArray[$g];
                        } else {
                            if ($index < count($foundWordArray)) {
                                if (ctype_upper($initialWord[$g]) && strtoupper($foundWordArray[$index]) == $initialWord[$g]) {
                                    $newWordReplaced .= strtoupper($foundWordArray[$index]);
                                    $index++;
                                } else {
                                    $newWordReplaced .= $foundWordArray[$index];
                                    $index++;
                                }
                            } else {
                                $newWordReplaced .= $initialWord[$g];
                            }
                        }
                    }
//                    var_dump('New word: ' . $newWordReplaced);
                    if ($wordStatus == 'correct') {
                        $array[$i] = '&' . $newWordReplaced . '&';
                    } else if ($wordStatus == 'error') {
                        $array[$i] = '/' . $newWordReplaced . '/';
                    } else if ($wordStatus == 'unknown') {
                        $array[$i] = '#' . $newWordReplaced . '#';
                    }

                    $similarWordsResult[$i]['word'] = $array[$i];
//                }
            }

        } else {
            $similarWordsResult = $string;
        }
//        print_r($array);
        $similarWordsResult['statistics'] = array('recognized' => $recognizedWords, 'fixed' => $fixedWords, 'unknown' => $unknownWords);
//        print_r($similarWordsResult);
        return $similarWordsResult;
    }

    private static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }
//
//    public static function process($string)
//    {
//        $recognizedWords = [];
//        $unknownWords = [];
//        $fixedWords = [];
//        $similarWordsResult = [];
//
//        $db = new PDO("mysql:host=localhost;dbname=wordnet;", 'root', '');
//        $array = self::parseInitialString($string);
//        if ($array) {
//            for ($i = 0; $i < count($array); $i++) {
//                $initialWord = $array[$i];              //save initial word
//                $newWord = self::processWord($array[$i]);
//
//                $array[$i] = strtolower($newWord);
//
//                if (in_array($array[$i], self::$partsOfSpeech)) {               //search word in array, if found => it is correct
//                    $array[$i] = '&' . $array[$i] . '&';
//                    $similarWordsResult[$i]['word'] = $array[$i];
//                } else {                                                        //search word in db, if found => it is correct
//                    if (strpos($array[$i], '\'ll') !== false || strpos($array[$i], '\'ve') !== false
//                        || strpos($array[$i], 'n\'t') !== false
//                    ) {
//                        $array[$i] = '&' . $array[$i] . '&';
//                        $similarWordsResult[$i]['word'] = $array[$i];
//                    }
//                    if (self::endsWith($array[$i], 's')) {
//                        if (substr($array[$i], 0, strlen($array[$i]) - 1)) {
//                            $array[$i] = substr($array[$i], 0, strlen($array[$i]) - 1);
//                        }
//                    }
//                    $stmt = $db->query("SELECT lemma FROM words WHERE lemma LIKE '" . $array[$i] .
//                        "' OR sample LIKE '% " . $array[$i] . " %' LIMIT 1");
//                    if (!$stmt)
//                        continue;
//                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//                    if ($result && ($result[0]['lemma'])) {
//                        $array[$i] = '&' . $array[$i] . '&';
//                        $similarWordsResult[$i]['word'] = $array[$i];
//                    } else {
//                        //find all words from array and from db, where distance = 1
//                        $similarWords = [];
//                        foreach (self::$partsOfSpeech as $word) {
//                            $distance = levenshtein($array[$i], $word, 1, 1, 1);
//                            if ($distance == 1) {
//                                array_push($similarWords, $word);
//                            }
//                        }
//
//                        $length = strlen($array[$i]);
//                        $in = " $length, $length + 1, $length - 1 ";
//                        $sql = "SELECT lemma FROM words WHERE wordGroup IN ($in) ";
//                        $globalStmt = $db->query($sql);
//                        if (!$globalStmt)
//                            continue;
//
//                        $dbWords = $globalStmt->fetchAll(PDO::FETCH_ASSOC);
//
//                        foreach ($dbWords as $dbWord) {
//                            if ($dbWord['lemma'] != null) {
//                                $distance = levenshtein($array[$i], $dbWord['lemma'], 1, 1, 1);
//                                if ($distance == 1) {
//                                    array_push($similarWords, $dbWord['lemma']);
//                                }
//                            }
//                        }
//                        //find which word has max frequency and suggest it
//                        //if there is no such word => suggest any
//
//                        $maxFrequency = -1;
//                        $suggestedWord = '';
//                        foreach ($similarWords as $word) {
//                            $sql = "SELECT frequency FROM `word-frequency` WHERE lemma LIKE '$word' LIMIT 1";
//                            $globalStmt = $db->query($sql);
//                            if (!$globalStmt)
//                                continue;
//                            $dbWord = $globalStmt->fetchAll(PDO::FETCH_ASSOC);
//                            if ($dbWord) {
//                                if ($dbWord[0]['frequency'] > $maxFrequency) {
//                                    $maxFrequency = $dbWord[0]['frequency'];
//                                    $suggestedWord = $word;
//                                }
//                            }
//                        }
//                        if ($suggestedWord != '') {
//                            $array[$i] = '/' . $suggestedWord . '/';
//                        } else {
//                            if (!empty($similarWords))
//                                $array[$i] = '/' . $similarWords[0] . '/';
//                            else
//                                $array[$i] = '#' . $array[$i] . '#';
//                        }
//
//                        if (count($similarWords) > 1) {
//                            $j = 0;
//                            $a = [];
//                            foreach ($similarWords as $similarWord) {
//                                if ($j == 2) break;
//                                if ($similarWord == $suggestedWord) continue;
//
//                                $a[] = $similarWord;
//                                $j++;
//                            }
//                            $similarWordsResult[$i]['similar-words'] = $a;
//                        }
//                    }
//                }
//
//                $newWordReplaced = '';
//                $wordStatus = '';
//
//                if ($array[$i][0] == '&' && $array[$i][strlen($array[$i]) - 1] == '&') {
//                    $wordStatus = 'correct';
//                    $resultString = trim($array[$i], "&");
//                    $recognizedWords[] = $resultString;
//                } else if ($array[$i][0] == '/' && $array[$i][strlen($array[$i]) - 1] == '/') {
//                    $wordStatus = 'error';
//                    $resultString = trim($array[$i], "/");
//                    $fixedWords[] = $resultString;
//                } else if ($array[$i][0] == '#' && $array[$i][strlen($array[$i]) - 1] == '#') {
//                    $wordStatus = 'unknown';
//                    $resultString = trim($array[$i], "#");
//                    $unknownWords[] = $resultString;
//                }
//
//                $foundWordArray = str_split($resultString);
//                $initialWordArray = str_split($initialWord);
//                $index = 0;
//                for ($g = 0; $g < count($initialWordArray); $g++) {
//                    if ($initialWordArray[$g] == '.' || $initialWordArray[$g] == ',' || $initialWordArray[$g] == '—' ||
//                        $initialWordArray[$g] == ';' || $initialWordArray[$g] == '!' || $initialWordArray[$g] == '?' ||
//                        $initialWordArray[$g] == ':' || $initialWordArray[$g] == '(' || $initialWordArray[$g] == ')' ||
//                        $initialWordArray[$g] == '{' || $initialWordArray[$g] == '}' || $initialWordArray[$g] == '"' ||
//                        $initialWordArray[$g] == '\''
//                    ) {
//                        $newWordReplaced .= $initialWordArray[$g];
//                    } else {
//                        if ($index < count($foundWordArray)) {
//                            if (ctype_upper($initialWord[$g]) && strtoupper($foundWordArray[$index]) == $initialWord[$g]) {
//                                $newWordReplaced .= strtoupper($foundWordArray[$index]);
//                                $index++;
//                            } else {
//                                $newWordReplaced .= $foundWordArray[$index];
//                                $index++;
//                            }
//                        } else {
//                            $newWordReplaced .= $initialWord[$g];
//                        }
//                    }
//                }
//                if ($wordStatus == 'correct') {
//                    $array[$i] = '&' . $newWordReplaced . '&';
//                } else if ($wordStatus == 'error') {
//                    $array[$i] = '/' . $newWordReplaced . '/';
//                } else if ($wordStatus == 'unknown') {
//                    $array[$i] = '#' . $newWordReplaced . '#';
//                }
//
//                $similarWordsResult[$i]['word'] = $array[$i];
//            }
//
//        } else {
//            $similarWordsResult = $string;
//        }
//        $similarWordsResult['statistics'] = array('recognized' => $recognizedWords, 'fixed' => $fixedWords, 'unknown' => $unknownWords);
//        return $similarWordsResult;
//    }

//    public static function insertMany()
//    {
//        $db = new PDO('mysql:host=localhost;dbname=wordnet;', 'root', '');
//        $file = fopen('C:\Users\sam13\Desktop\ttt.txt', "r") or die("Unable to open file!");
//        $words = [];
//        while (!feof($file)) {
//            $words[] = trim(fgets($file));
//        }
//        fclose($file);
//
//        $file = fopen('C:\Users\sam13\Desktop\fff.txt', "r") or die("Unable to open file!");
//        $fr = [];
//        while (!feof($file)) {
//            $fr[] = trim(fgets($file));
//        }
//        fclose($file);
//
//        foreach ($words as $index => $word)
//            $db->exec("INSERT INTO `word-frequency` (`lemma`, `frequency`) VALUES ('$words[$index]', '$fr[$index]')");
//    }



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