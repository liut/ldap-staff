<?PHP

/**
 * Blowfish
 *
 * @package ldap
 *
 **/
class Blowfish
{

	/**
	 * Encryption using blowfish algorithm
	 *
	 * @param string Original data
	 * @param string The secret
	 * @return string The encrypted result
	 * @author lem9 (taken from the phpMyAdmin source)
	 */
	public static function encrypt($data, $secret=null) {

		# If our secret is null or blank, get the default.
		if ($secret === null || ! trim($secret))
			$secret = isset($_SESSION['BLOWFISH_SALT'])? $_SESSION['BLOWFISH_SALT'] : session_id();

		# If the secret isnt set, then just return the data.
		if (! trim($secret))
			return $data;

		if (!function_exists('mcrypt_module_open')) {
			throw new Exception('need mcrypt extension');
		}

		if (empty($data)) {
			return $data;
		}

		$td = mcrypt_module_open(MCRYPT_BLOWFISH,'',MCRYPT_MODE_ECB,'');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_DEV_URANDOM);
		mcrypt_generic_init($td,substr($secret,0,mcrypt_enc_get_key_size($td)),$iv);
		$encrypted_data = base64_encode(mcrypt_generic($td,$data));
		mcrypt_generic_deinit($td);

		return $encrypted_data;
	}

	/**
	 * Decryption using blowfish algorithm
	 *
	 * @param string Encrypted data
	 * @param string The secret
	 * @return string Original data
	 * @author lem9 (taken from the phpMyAdmin source)
	 */
	public static function decrypt($encdata, $secret=null) {

		# This cache gives major speed up for stupid callers :)
		static $CACHE = array();

		if (isset($CACHE[$encdata]))
			return $CACHE[$encdata];

		# If our secret is null or blank, get the default.
		if ($secret === null || ! trim($secret))
			$secret = isset($_SESSION['BLOWFISH_SALT'])? $_SESSION['BLOWFISH_SALT'] : session_id();

		# If the secret isnt set, then just return the data.
		if (! trim($secret))
			return $encdata;

		if (!function_exists('mcrypt_module_open')) {
			throw new Exception('need mcrypt extension');
		}

		if (empty($encdata)) {
			return $encdata;
		}

		$td = mcrypt_module_open(MCRYPT_BLOWFISH,'',MCRYPT_MODE_ECB,'');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_DEV_URANDOM);
		mcrypt_generic_init($td,substr($secret,0,mcrypt_enc_get_key_size($td)),$iv);
		$decrypted_data = trim(mdecrypt_generic($td,base64_decode($encdata)));
		mcrypt_generic_deinit($td);

		return $decrypted_data;
	}

} // END class Blowfish
