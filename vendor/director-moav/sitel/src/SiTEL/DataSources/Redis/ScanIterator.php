<?php namespace SiTEL\DataSources\Redis;
/**
 * @author itaymoav
 */
class ScanIterator{
    /**
     * 
     * @var integer 
     */
    public  int $fetch_count   = 0;
    
    /**
     * 
     * @var \SiTEL\DataSources\Redis\iScannable 
     */
    private \SiTEL\DataSources\Redis\iScannable $key_boss;
    
    /**
     * @param \SiTEL\DataSources\Redis\iScannable $key_boss
     */
    public function __construct(\SiTEL\DataSources\Redis\iScannable $key_boss){
        $this->key_boss = $key_boss;
    }
    
    /**
     * Iterate this
     * @return \Generator<int,mixed>
     */
    public function fetchAll(){
        $cursor        = null;
        while(($row = $this->key_boss->scan_me($cursor))!== false) {
            if(count($row) > 0) {
                foreach($row as $value) {
                    yield $value;
                    $this->fetch_count++;
                }
            }
        }
    }
}
