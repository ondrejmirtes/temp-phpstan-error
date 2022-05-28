<?php namespace SiTEL\UIUtils\Html\Form;

/**
 * Utility functions
 */
abstract class Utilities
{
    /**
     * array of data to options ready structure
     * 
     * expects [['value_alias' =>111,'label_alias'=>'tototo'],['value_alias' =>111,'label_alias'=>'tototo']........['value_alias' =>111,'label_alias'=>'tototo']]
     *
     * @param array<array<string, string>> $data
     * @param string $value_alias
     * @param string $label_alias
     * @return array<string>
     */
    static public function data_to_key_value(array $data,string $value_alias='id',string $label_alias='title'):array{
        $options = [];
        foreach($data as $k){
            $options[$k[$value_alias]] = $k[$label_alias];
        }
        return $options;
    }
}
