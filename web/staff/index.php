<?PHP

include_once dirname(dirname(__DIR__)) . '/inc/init.php';

Loader::import(__DIR__ . DS . '_class');

Dispatcher::farm([
	//'default_controller' => 'home',
	'default_action' => 'index',
	'view_ext' => '.htm.php'
])->run();
