<?php
/**
 * Replcae whitespace to  space
 * @author Naghmeh
 */
class Form_Filter_SpecialCharacter implements Form_Filter_i{
    public function filter($data){
        $data =  str_replace('&nbsp;', ' ', $data);
        $data =  str_replace("\t", ' ', $data);//tab
        $data =  str_replace("\n", ' ', $data);//new line
        $data =  str_replace("\r", ' ', $data);//carriage return
        $data =  str_replace("\0", ' ', $data);//NULL-byte
        $data =  str_replace("\x0B", ' ', $data);//vertical tab
        return $data;
    }
}