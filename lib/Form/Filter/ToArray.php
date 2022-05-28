<?php
/**
 * Cast the value to array
 * @author prestton
 */
class Form_Filter_ToArray implements Form_Filter_i{
    public function filter($data){
        return (array)$data;
    }
}