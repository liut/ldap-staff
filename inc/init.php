<?PHP

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

include_once __DIR__ . DS . 'config.php';

// include_once APP_ROOT . 'vendor' . DS . 'autoload.php';

// Global Loader
include_once LIB_ROOT . 'class'.DS.'Loader.php';

Loader::init();

Loader::import(APP_ROOT . 'class', TRUE);
Loader::import(WEB_ROOT . '_class', TRUE);

if (defined('_PS_DEBUG') && TRUE === _PS_DEBUG) {
	set_error_handler(['Loader','printError']);
}

if (PHP_SAPI === 'cli') { // command line
	isset($argc) || $argc = $_SERVER['argc'];
	isset($argv) || $argv = $_SERVER['argv'];
}
elseif (isset($_SERVER['HTTP_HOST'])) { // http mod, cgi, cgi-fcgi
	if(headers_sent()) {
		exit('headers already sent');
	}

	if (defined('RESPONSE_NO_CACHE')) {
		header('Expires: Fri, 02 Oct 98 20:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
	}
}

include_once __DIR__ . '/ldap.func.php';

if (@session_id())
	return;

$sess_name = getenv('APP_SESSION');
$sess_name && @session_name($sess_name);
@session_start();

