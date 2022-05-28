<?php namespace SiTEL\DataSources\Redis;
/**
 * @author itaymoav
 */
interface iDataBuilder{

  /**
   * 
   * @return array<string,string>
   */
	public function build();
	/**
	 * number of seconds until dies.
	 * Send 0 for infinite
	 * @return int
	 */
	public function ttl():int;
}


