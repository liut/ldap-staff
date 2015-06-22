<?PHP

/**
 * undocumented class
 *
 * @package web
 * @author liut
 **/
class Controller_Sign extends Controller
{
	public function action_in()
	{
		$current_user = Staff::current();

		return ['staff/signin',
			['current_user' => $current_user]
		];
	}

	public function action_in_post()
	{
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

		$user = Staff::signin($username, $password);
		if (is_object($user)) {
			return [TRUE, $user];
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
