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
		if (!$ldapconn) {
			return [FALSE, 'error' => ['message' => '连接服务器失败: Could not connect to LDAP server.',
			'field' => 'username']];
		}

		// Set some ldap options for talking to
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

		// using ldap bind
		$bind_fmt = defined('LDAP_BIND_FORMAT') ? LDAP_BIND_FORMAT : 'uid=%s,ou=people,'.LDAP_BASE_DN;
		$ldaprdn  = sprintf( $bind_fmt, $username);     // ldap rdn or dn
		$ldapbind = FALSE;

		$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $password);
		if (!$ldapbind) {
			return [FALSE,
				'error' => [
					'message' => '密码错误: '.ldap_error($ldapconn),
					'field' => 'old_password'
				]
			];
		}

		$userdata = array('userPassword' => password_hash_custom($new_password, LDAP_PASSWORD_HASH));
		if(ldap_modify($ldapconn, $ldaprdn, $userdata)) {
			return TRUE; // 密码修改成功
		}

		return [FALSE,
			'error' => [
				'message' => '密码修改失败: ',
				'field' => 'new_password'
			]
		];


	}
} // END class Controller_Password
