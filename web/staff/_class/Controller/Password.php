<?PHP

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class Controller_Password extends Controller
{
	private $ldapconn;

	public function afterAction(& $response = NULL)
	{
		if (is_resource($this->ldapconn)) {
			ldap_unbind($this->ldapconn);
		}
	}

	public function action_index()
	{
		return 'staff/password';
	}

	public function action_change_post()
	{

		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
		$password = filter_input(INPUT_POST, 'old_password', FILTER_UNSAFE_RAW);
		$new_password = filter_input(INPUT_POST, 'new_password', FILTER_UNSAFE_RAW);

		if (strlen($new_password) < 8) {
			return [FALSE, 'error' => ['message' => '密码太短', 'field' => 'new_password']];
		}

		if ($new_password == $password) {
			return [FALSE, 'error' => ['message' => '密码没有改变', 'field' => 'new_password']];
		}

		$ldap = Da_Wrapper::dbo('staff.people');
		if (!$ldap->connect()) {
			return [FALSE, 'error' => ['message' => '连接服务器失败: Could not connect to LDAP server.',
			'field' => 'username']];
		}

		$rdn = $ldap->rdn($username);
		$ldapbind = $ldap->bind($rdn, $password);
		if (!$ldapbind) {
			return [FALSE,
				'error' => [
					'message' => '密码错误: '.$ldap->error(),
					'field' => 'old_password'
				]
			];
		}

		$userdata = array('userPassword' => password_hash_custom($new_password, LDAP_PASSWORD_HASH));
		if($ldap->modify($rdn, $userdata)) {
			return [TRUE]; // 密码修改成功
		}

		return [FALSE,
			'error' => [
				'message' => '密码修改失败: ',
				'field' => 'new_password'
			]
		];


	}
} // END class Controller_Password
