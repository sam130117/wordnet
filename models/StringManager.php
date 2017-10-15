<?php
namespace models;


class StringManager
{
    public static function process($string)
    {
        $array = preg_split( '/[, |. |; |: |! |? |!| |-|.|:|;|?]/', $string);
        if($array){
            $array = array_filter($array, function($value) { return $value !== ''; });
        }
        else{
            $array = $string;
        }
        return $array;
    }
}