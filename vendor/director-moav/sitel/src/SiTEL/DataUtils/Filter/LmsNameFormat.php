<?php namespace SiTEL\DataUtils\Filter;
/**
 * Formats a name according to what we
 * expects in SiTEL
 *
 * @author Itay
 */
class LmsNameFormat implements iString{
   /**
    * {@inheritDoc}
    * @see \SiTEL\DataUtils\Filter\iString::filter()
    */
    public function filter($data):string{
        $data = str_replace(['\'',','] ,[''],$data);
        if (substr($data,0,2)=='mc'){ // we donot want to change Kimchi to kiMchi
            $data   = str_replace('mc','mc-',$data);
        }
        $tmp=explode('-',$data);
        $patterns =['/Mc([A-Z])/','/De([A-Z])/','/Di([A-Z])/'];
        $replacements = ['yyyy $1','xxxx $1','zzzz $1'];
        
        foreach($tmp as &$name){
            $name=preg_replace($patterns,$replacements,$name);
            $name=ucwords(strtolower($name??''));
            $name = str_replace(["Yyyy ","Xxxx ","Zzzz "],['Mc','De','Di'], $name);
        }
        return join('',$tmp);
    }
}