<?PHP

include_once __DIR__ . '/config.php';

if (!defined('LDAP_HOST')) {
	die('LDAP_HOST not found');
}

if (!defined('LDAP_BASE_DN')) {
	die('LDAP_BASE_DN not found');
}

defined('LDAP_PORT') || define('LDAP_PORT', 389);
defined('LDAP_PASSWORD_HASH') || define('LDAP_PASSWORD_HASH', 'blowfish');

include_once __DIR__ . '/ldap.func.php';
