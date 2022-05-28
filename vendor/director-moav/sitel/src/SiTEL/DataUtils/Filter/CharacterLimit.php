<?php namespace SiTEL\DataUtils\Filter;
/**
 * Filter input to limit it to length of character_limit
 * @author Naghmeh
 */
class CharacterLimit implements i{
	/**
	 * 
	 * @var int $character_limit
	 */
    private int $character_limit;
	
	/**
	 * 
	 * @param int $character_limit
	 */
	public function __construct(int $character_limit){
		$this->character_limit	= $character_limit;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \SiTEL\DataUtils\Filter\i::filter()
	 */
    public function filter($data):string{
        return substr($data,0,$this->character_limit);
    }
}