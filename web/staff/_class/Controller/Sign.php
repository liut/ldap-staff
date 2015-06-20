<?PHP

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class Controller_Sign extends Controller
{
	public function action_in()
	{
		// $ldap = Da_Wrapper::dbo('staff.people');

		return 'staff/signin';
	}

	public function action_in_post()
	{
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

		$ldap = Da_Wrapper::dbo('staff.people');
		$bind = $ldap->bind($ldap->rdn($username), $password);
		if ($bind) {
			return [TRUE];
		}

		return [FALSE,
			'error' => [
				'message' => '密码错误: '.$ldap->error(),
				'field' => 'password'
			]
		];

	}

	public function action_out()
	{
	}

} // END class Controller_Sign
