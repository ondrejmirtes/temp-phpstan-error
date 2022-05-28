<?php namespace SiTEL\Encryption;

/**
 * This class encrypts using 
 * AES 256 algoritem
 * CBC packing mode
 * 
 * Copied from https://github.com/simplesamlphp/simplesamlphp/issues/228 
 * 
 * @author itaymoav
 */
class AES256CBC{
    const ZERO_PADDING = true; 
		
    /**
     * @var string $method
     * @var string $key
     * @var string $iv
     * @var integer $iv_size
     * @var integer $block_size
     */
	private 	$method = 'AES-256-CBC',
				/**
				 * @var string an encoded key, this is the shared private secret we use to decode/encode
				 */
				$key    = '',
				/**
				 * @var string
				 */
				$iv     = '',
				/**
				 * @var int
				 */
				$iv_size = 0,
				/**
				 * @var int Size of the block of the clear data to be encrypted 
				 */
				$block_size = 32
	;
	
	/**
	 * 
	 * @param string $key base64 decoded please.
	 * @param ?string $iv (optional, depends if I am the decrypting or encrypting guy)
	 */
	public function __construct(string $key,?string $iv=''){
		$this->key  = $key;
		$iv_size = openssl_cipher_iv_length($this->method);
		if(!$iv_size){
		    throw new \Exception("IV size is wrong for this method [{$this->method}]");
		}
		$this->iv_size = $iv_size; 
		$this->iv = $iv?base64_decode($iv):$this->generate_iv();
		
	}
	
	/**
	 * Openssl generated IV
	 * @return string
	 */
	protected function generate_iv():string{
	    $iv = openssl_random_pseudo_bytes($this->iv_size);
	    if(!$iv){
	        throw new \Exception("Could not generate IV with size[{$this->iv_size}]");
	    }
		return $iv;
	}
	
	/**
	 * getter for iv
	 * @return string base 64 encoded iv string
	 */
	public function iv():string{
		return base64_encode($this->iv);
	}
	
	/**
	 * Debugs the openssl stuff
	 * @param string $data usually the un encrypted string I wish to encrypt
	 */
	public function debug_properties($data):void{
	    echo "AVBAILABLE CYPHERS:\n";
	    print_r(openssl_get_cipher_methods());
	    echo "\n----------------------\n";
	    echo "openssl version text: " . OPENSSL_VERSION_TEXT . "\n";
	    echo "openssl version number:  " . OPENSSL_VERSION_NUMBER . "\n";
	    echo "\ndata {$data}\n";
	    echo "\nmethod {$this->method}\n";
	    echo "\nkey {$this->key}\n";
	    echo "\npadding " . OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING . "\n";
	    echo "\niv {$this->iv}\n";
	     
	}
	
	/**
	 * Encrypts
	 * 
	 * @param string $clear un encrypted
	 * @return string encrypted data
	 */
	public function encrypt($clear,bool $zero_padding=false):string{
	    //$this->debug_properties($clear);
	    
	    //pad data
	    $pad = $this->block_size - (strlen($clear) % $this->block_size);
	    //PKCS#7 is using the ascii code of the character as the amount of padding 
	    $padding_char = $zero_padding? chr(0) : chr($pad);
	    $clear .= str_repeat($padding_char, $pad);
	    
        $encrypted_data = openssl_encrypt ($clear, $this->method, $this->key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->iv);
        $err = openssl_error_string();
        if(!$encrypted_data){
            throw new \Exception("Failed ENcryption with [{$err}]");
        }
		return base64_encode($encrypted_data);		
	}
	
	/**
	 * decrypts
	 * 
	 * @param string $encrypted_data
	 * @return string
	 */
	public function decrypt(string $encrypted_data):string{
	    $decoded_data = base64_decode($encrypted_data);
        $clear = openssl_decrypt($decoded_data, $this->method, $this->key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->iv);
        $err = openssl_error_string();
	    if(!$clear){
            throw new \Exception("Failed DEcryption with [{$err}]");
        }
        //check if the CLEAR string has the padding and remove them.
        $last_char_code = ord(substr($clear, -1));
        if($last_char_code == ord(substr($clear, -1 * $last_char_code,1))){
            $clear = substr($clear,0,strlen($clear) - $last_char_code);
        }
        return $clear;
    }
}
