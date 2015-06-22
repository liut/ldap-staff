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

	const SK = 'USER';

	protected static $_stored_keys = array('uid','displayName','lastHit');

	protected static $_current;

	public static function findByPk($uid, $pk = 'uid')
	{
		throw new DomainException('not implementation');
	}

	/**
	 * login
	 */
	public static function signin($login, $password)
	{
		$ldap = static::dao();
		$bind = $ldap->login(strtolower($login), $password);
		if (!$bind) {
			return [FALSE, 'error' => [
				'message' => '密码错误: '.$ldap->error(),
				'field' => 'password'
			]];
		}

		$result = $ldap->read($ldap->rdn($login), '(objectClass=inetOrgPerson)', static::$_attributes);
		if (!$result) {
			return [FALSE, 'error' => [
				'message' => 'read error: '.$ldap->error(),
				// 'field' => 'username'
			]];
		}
		$entries = $ldap->get_entries($result);
		// print_r($entries);
		// var_dump($ldap->error());

		$staff = [];
		foreach (static::$_attributes as $key) {
			$k = strtolower($key);
			if (isset($entries[0][$k][0])) {
				$staff[$key] = $entries[0][$k][0];
			}
		}

		$user = static::farm($staff);
		$user->isNew(FALSE);
		// $user = static::authenticate(strtolower($login), $password);
		// if (!is_object($user)) {
		// 	// 直接返回错误编号
		// 	return $user;
		// }
		//$callback = function($ret, &$row){
			//var_dump($ret, $row);exit;
		//	return $ret;
		//};
		//static::bind('after_update', $callback);
		return static::signinDirect($user);
	}

	public static function signinDirect(AccountBase $user)
	{
		$user->last_logined = date('Y-m-d H:i:s');
		$user->last_login_ip = Request::current()->CLIENT_IP;
		$user->times_login ++;
		// $user->last_tsid = Bb_Ticket::current()->id;
		// $user->save();
		$_SESSION[self::SK] = $user;
		static::setHttpCookie($user);

		return $user;
	}

	protected static function saveLogin($login, $password)
	{
		$user = ['uid' => Blowfish::encrypt($login), 'pass' => Blowfish::encrypt($password)];
		$_SESSION['BOUND'] = $user;
	}

	protected static function loadLogin()
	{
		$user = $_SESSION['BOUND'];
		if (!isset($user) || !isset($user['name'])) {
			return FALSE;
		}

		return ['name' => Blowfish::decrypt($user['name']), 'pass' => Blowfish::decrypt($user['pass'])];
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

} // END class Staff
