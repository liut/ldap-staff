<?PHP

include_once dirname(dirname(__DIR__)) . '/inc/init.php';

Loader::import(__DIR__ . DS . '_class');

Dispatcher::farm([
	'account_call' => 'Staff::current',
	//'default_controller' => 'home',
	'default_action' => 'index',
	'view_call' => function($dispatcher) {
		$loader = new Twig_Loader_Filesystem(SKIN_ROOT.'default'); // Twig_Loader_Filesystem
		$twig = new Twig_Environment($loader, array(
		    'cache' => VIEW_CACHE_DIR,
		    'debug' => defined('_PS_DEBUG') && TRUE === _PS_DEBUG
		));
		return $twig;
	},
	'view_ext' => '.htm'
])->run();
