<?php namespace SiTEL\DataSources\Redis;
/**
 * @author itaymoav
 */
interface iScannable{
    /**
     * 
     * @param ?int $cursor
     * @param string|boolean $pattern
     * @return array<int,mixed>|false
     */
    public function scan_me(?int &$cursor,$pattern=false);
}
