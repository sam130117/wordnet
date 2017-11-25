<?php
namespace models;


class StringManager
{
    static $partsOfSpeech = array( 'I', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'mine', 'yours',
        'his', 'hers', 'its', 'ours', 'theirs', 'myself', 'yourself', 'himself', 'herself', 'itself', 'ourselves',
        'yourselves', 'themselves', 'who', 'whom', 'which', 'that', 'whoever', 'whomever', 'whose', 'why', 'when',
        'this', 'these', 'that', 'those',

        'a', 'an', 'the',

        'on', 'in', 'at', 'since', 'for', 'ago', 'during', 'before', 'after', 'until', 'till', 'to',
        'past', 'from', 'by', 'off', 'beside', 'under', 'over', 'below', 'above', 'up', 'down', 'across', 'through',
        'into', 'out of', 'onto', 'towards', 'of', 'about', 'with', 'throughout', 'against', 'along', 'among', 'around',
        'behind',

        'and', 'also', 'nor', 'or', 'else', 'but', 'whereas', 'while', 'yet', 'even', 'though', 'although',
        'just', 'as', 'both', 'neither', 'because', 'so', 'if', 'then', 'once', 'here', 'there',
        'absolutely', 'achoo', 'ack', 'ahh', 'aha', 'ahem', 'ahoy', 'agreed', 'alas', 'alright',

        'alrighty', 'alack', 'anytime', 'argh', 'anyhoo', 'anyhow', 'attaboy', 'attagirl', 'aww', 'awful', 'bam', 'bah',
        'humbug', 'behold', 'bingo', 'blah', 'boo', 'bravo', 'cheers', 'crud', 'darn', 'dang', 'doh', 'drat', 'duh',
        'eek', 'eh', 'gee', 'geepers', 'whiz', 'golly', 'goodness', 'gosh', 'ha', 'hallelujah', 'hey', 'hi', 'hmmm',
        'huh', 'indeed', 'no', 'nah', 'oops', 'ouch', 'phew', 'shucks', 'tut', 'uggh', 'waa', 'woah', 'woops', 'wow',
        'yay', 'yikes' );


    public static function process($string)
    {
        $array = preg_split( "/[,|.|;|:|!|?|!| |-|.|:|;|?|’|‘|\n|\r|]/", $string, -1, PREG_SPLIT_NO_EMPTY);
        if($array){
            for ($i = 0; $i < count($array)-1; $i++) {

                if(in_array($array[$i], self::$partsOfSpeech))
                {
                    //if word is in array -> remove it because it is correct
                    $array[$i] = '..';
                }
                else {
                    foreach (self::$partsOfSpeech as $partsOfSpeech) {
                        //find min levenshtein distance among $partsOfSpeech
                        //than find min levenshtein distance among wordnet
                        //choose less
                        $distance = levenshtein($array[$i], $partsOfSpeech, 1, 1,1);
                    }

                }
            }
        }
        else{
            $array = $string;
        }
        return $array;
    }
}