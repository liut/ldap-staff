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
	protected static $_attributes = ['dn','cn', 'displayName', 'employeeNumber', 'mail', 'mobile', 'sn', 'uid', 'userPassword'];
	protected static $_api_keys = ['cn', 'displayName', 'employeeNumber', 'mail', 'mobile', 'sn', 'uid'];

	const COOKIE_NAME = '_lcgc';
	const COOKIE_LIFE = 604800;//	60*60*24*7;
	const ENCRYPT_KEY = "k9E4JHl7G0PFw7jz";	// must be 16 chars

	const FIELD_LOGIN   = 'uid';			// 登录名字段

	const kSESS = 'USER';
	const FILTER_PEOPLE = '(objectClass=inetOrgPerson)';
	const FILTER_GROUP = '(objectClass=groupOfNames)';

	protected static $_stored_keys = array('uid','displayName','sn','cn','lastHit');

	protected static $_current;

	public static function findByPk($uid, $pk = 'uid')
	{
		$ldap = static::dao();
		$result = $ldap->read($ldap->rdn($uid), static::FILTER_PEOPLE, static::$_attributes);
		if (!$result) {
			return [FALSE, 'error' => [
				'message' => 'read error: '.$ldap->error(),
			]];
		}
		$entries = $ldap->get_entries($result);
		if (!$entries || $entries['count'] == 0) {
			return [FALse, 'error' => [
				'message' => 'not found. '.$ldap->error(),
			]];
		}
		$entry = $entries[0];

		$staff = [];
		foreach (static::$_attributes as $key) {
			$k = strtolower($key);
			if (isset($entry[$k])) {
				if (is_string($entry[$k])) {
					$staff[$key] = $entry[$k];
				} elseif (is_array($entry[$k]) && isset($entry[$k][0])) {
					$staff[$key] = $entry[$k][0];
				} else {
					Log::notice($entry[$k], __MEHTOD__.' error entry value');
				}

			}
		}

		return $staff;
	}

	public static function authenticate($login, $password, $option = NULL)
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

	public static function loadKeepers()
	{
		static $CACHE_KEEPERS = [];
		if (static::restoreLogin()) {
			$ldap = static::dao();
			$limit = 10;
			$sr = $ldap->read('cn=keeper,'.$ldap->baseDn('groups'), static::FILTER_GROUP, ['cn', 'member'], FALSE, $limit);
			$entries = $ldap->get_entries($sr);
			$entry = $entries[0];
			if (isset($entry['member']) && $entry['member']['count'] > 0) {
				unset($entry['member']['count']);
				$CACHE_KEEPERS = $entry['member'];
			}
		}

		return $CACHE_KEEPERS;
	}

	public static function findAll($limit = NULL, $offset = 0)
	{
		// TODO:
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

	public function isKeeper()
	{
		return in_array($this->dn, static::loadKeepers());
	}

} // END class Staff
