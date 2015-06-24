<?PHP


/**
 * undocumented class
 * uid=liutao,ou=people,dc=licaigc,dc=net
 *
 * @package default
 * @author liut
 **/
class Staff extends AccountBase
{
	protected static $_db_name = 'staff.people';
	// protected static $_table_name = 'members';
	protected static $_primary_key = 'uid';
	protected static $_cachable = 0;
	protected static $_editables = ['userPassword','displayName','mobile','mail'];
	protected static $_attributes = ['cn', 'displayName', 'employeeNumber', 'mail', 'mobile', 'sn', 'uid', 'userPassword'];

	const COOKIE_NAME = '_lcgc';
	const COOKIE_LIFE = 604800;//	60*60*24*7;
	const ENCRYPT_KEY = "k9E4JHl7G0PFw7jz";	// must be 16 chars

	const FIELD_LOGIN   = 'uid';			// 登录名字段

	const kSESS = 'USER';
	const FILTER = '(objectClass=inetOrgPerson)';

	protected static $_stored_keys = array('uid','displayName','sn','cn','lastHit');

	protected static $_current;

	public static function findByPk($uid, $pk = 'uid')
	{
		$ldap = static::dao();
		$result = $ldap->read($ldap->rdn($uid), static::FILTER, static::$_attributes);
		if (!$result) {
			return [FALSE, 'error' => [
				'message' => 'read error: '.$ldap->error(),
				// 'field' => 'username'
			]];
		}
		$entries = $ldap->get_entries($result);

		$staff = [];
		foreach (static::$_attributes as $key) {
			$k = strtolower($key);
			if (isset($entries[0][$k][0])) {
				$staff[$key] = $entries[0][$k][0];
			}
		}

		return $staff;
	}

	public static function authenticate($login, $password)
	{
		$login = strtolower($login);
		$ldap = static::dao();
		$bind = $ldap->login($login, $password);
		if (!$bind) {
			return [FALSE, 'error' => [
				'message' => '密码错误: '.$ldap->error(),
				'field' => 'password'
			]];
		}

		static::saveLogin($login, $password);

		$data = static::findByPk($login);
		if (isset($data[0]) && is_bool($data[0])) {
			return $data;
		}

		$user = static::farm($data);
		$user->isNew(FALSE);
		return $user;
	}

	/**
	 * login
	 */
	public static function signin($login, $password)
	{
		$user = static::authenticate($login, $password);
		if (!is_object($user)) {
			// 直接返回错误编号
			return $user;
		}

		return static::signinDirect($user);
	}

	public static function signinDirect(AccountBase $user)
	{
		$user->last_logined = date('Y-m-d H:i:s');
		$user->last_login_ip = Request::current()->CLIENT_IP;
		$user->times_login ++;
		// $user->last_tsid = Bb_Ticket::current()->id;
		// $user->save();
		$_SESSION[self::kSESS] = $user;
		static::setHttpCookie($user);

		return $user;
	}

	protected static function saveLogin($login, $password)
	{
		$user = ['uid' => Blowfish::encrypt($login), 'pass' => Blowfish::encrypt($password)];
		$_SESSION['BOUND'] = $user;
	}

	public static function restoreLogin()
	{
		$user = isset($_SESSION['BOUND']) ? $_SESSION['BOUND'] : NULL;
		if (!isset($user) || !isset($user['uid']) || !isset($user['pass'])) {
			return FALSE;
		}

		$ldap = static::dao();
		return $ldap->login(Blowfish::decrypt($user['uid']), Blowfish::decrypt($user['pass']));
	}

	protected static function retrieve()
	{
		$user = parent::retrieve();

		if ($user->isLogin() && static::restoreLogin()) {
			$data = static::findByPk($user->uid);
			if (isset($data[0]) && is_bool($data[0])) {
				return $user;
			}

			if ($user->uid == $data['uid']) {
				unset($data['uid']);
				$user->set($data);
			}
		}

		return $user;
	}

	/**
	 * name
	 *
	 * @return string
	 */
	public function getName()
	{
		if ($this->__isset('displayName')) {
			return $this->displayName;
		}
		if ($this->__isset('fullname')) {
			return $this->fullname;
		}
		return $this->uid;
	}

	public function getFullname()
	{
		return $this->sn . ' ' . $this->cn;
	}

} // END class Staff
