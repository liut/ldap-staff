<?PHP

/**
 * undocumented class
 *
 * @package web
 * @author liut
 **/
class Controller_Sign extends Controller
{
	private function referer()
	{
		$referer = $this->_request->get('referer', 'url');

		if (empty($referer)) {
			$referer = $this->_request->HTTP_REFERER;
		}

		if (empty($referer)) {
			$referer = '/';
		}

		return $referer;
	}

	public function action_in()
	{
		return ['staff/signin',
			['referer' => $this->referer()]
		];
	}

	public function action_in_post()
	{
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

		$user = Staff::signin($username, $password);
		if (is_object($user)) {
			return [TRUE, [
				'user' => $user,
				'referer' => $this->referer()]
			];
			// return [302, '/'];
		}

		return $user;

		// return [FALSE,
		// 	'error' => [
		// 		'message' => '密码错误: '.$ldap->error(),
		// 		'field' => 'password'
		// 	]
		// ];

	}

	public function action_out()
	{
		Staff::current()->signout();
		return [302, '/'];
	}

} // END class Controller_Sign
