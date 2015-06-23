<?PHP

if(defined('APP_ROOT')) return;	// 防止重复定义

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('CONF_ROOT') || define('CONF_ROOT', __DIR__ . DS );

define('APP_ROOT', dirname(__DIR__) . DS);
define('LIB_ROOT', APP_ROOT . 'library' . DS);
define('WEB_ROOT', APP_ROOT . 'web' . DS);
define('SKIN_ROOT', APP_ROOT . 'skins' . DS);

define('LOG_ROOT', '/var/log/app/');
define('TEMP_ROOT', '/var/tmp/app/' );

// writable paths
define('CACHE_ROOT', WEB_ROOT.'cache/' );	//
define('DATA_ROOT', WEB_ROOT.'data/' );	//

if('WINNT' == PHP_OS || 'Darwin' == PHP_OS || isset($_SERVER['LOCAL_DEV'])) // 为 windows & macosx 下调试用，仅 beta 和 开发 环境
{
	defined('LOG_LEVEL') || define('LOG_LEVEL', 6 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN', 'example.dev' );

	defined('_PS_DEBUG') || define('_PS_DEBUG', TRUE );	// DEBUG , beta only
	defined('_DB_DEBUG') || define('_DB_DEBUG', TRUE );	// DEBUG , beta only

	define('APP_SESSION', '_sess_dev');
}
else
{
	defined('LOG_LEVEL') || define('LOG_LEVEL', 5 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN', 'example.info' );	// 主域名
	define('APP_SESSION', '_sess');
}

define('LDAP_HOST', 'slapd');
define('LDAP_PORT', 389);
define('LDAP_BASE_DN', 'dc=example,dc=org');

define('LDAP_PASSWORD_HASH', 'ssha');


// 输出字符集
defined('RESPONSE_CHARSET') || define('RESPONSE_CHARSET', 'utf-8' );

// view / smarty
define('VIEW_CLASS', 'View_Simple');
//defined('VIEW_TEMPLATE_DIR') || define('VIEW_TEMPLATE_DIR', SKIN_ROOT . 'default/' );
define('VIEW_COMPILE_DIR', CACHE_ROOT . 'view/tpl_c' );
define('VIEW_CACHE_DIR', CACHE_ROOT . 'view/cache' );
define('VIEW_CONFIG_DIR', CONF_ROOT .'view/' );
define('VIEW_SKINS_ROOT', SKIN_ROOT );
define('VIEW_SKINS_AVAILABLE', 'default' ); // default skin1 skin2
define('VIEW_SKIN_DEFAULT', 'default' );
define('VIEW_SKIN_CURRENT', 'default' );

