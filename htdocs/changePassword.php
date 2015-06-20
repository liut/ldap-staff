<?PHP

include_once __DIR__ . '/../inc/init.php';

$ret = array(
	array(), 	// username
	array(), 	// password
	array() 	// new_password
);

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
$new_password = filter_input(INPUT_POST, 'password_new', FILTER_UNSAFE_RAW);

if (strlen($new_password) < 8) {
	$ret[2] = array('password_new', false, '密码太短');
	die(json_encode($ret));
}

if ($new_password == $password) {
	$ret[2] = array('password_new', false, '密码没有改变');
	die(json_encode($ret));
}

$ldapconn = ldap_connect(LDAP_HOST, LDAP_PORT);
if (!$ldapconn) {
	$ret[0] = array('username', false, '连接服务器失败: Could not connect to LDAP server.');
	die(json_encode($ret));
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
	$ret[1] = array('password', false, '密码错误: '.ldap_error($ldapconn));
	die(json_encode($ret));
}

$userdata = array('userPassword' => password_hash_custom($new_password, LDAP_PASSWORD_HASH));
if(ldap_modify($ldapconn, $ldaprdn, $userdata)) {
	$ret[2] = array('password_new', true, '密码修改成功');
} else {
	$ret[2] = array('password_new', false, '密码修改失败');
}

ldap_unbind($ldapconn);

echo json_encode($ret);


