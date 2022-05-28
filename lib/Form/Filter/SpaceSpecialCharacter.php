<?php
/**
 * Change &nbsp; to space
 * @author Itay
 */
class Form_Filter_SpaceSpecialCharacter implements Form_Filter_i{
    public function filter($data){
        return str_replace('&nbsp;', ' ', $data);
    }
}