<?PHP

/**
 * undocumented class
 *
 * @package web
 * @author liut
 **/
class Controller_Home extends Controller
{
	public function action_index($value='')
	{
		$current_user = Staff::current();

		return ['staff/home', [
			'current_user' => $current_user
		]];
	}

	public function action_pi()
	{
		if ($this->_request->CLIENT_IP != '127.0.0.1') {
			return 404;
		}

		phpinfo(16+32);
		// phpinfo();
	}
} // END class Controller_Home
