<?PHP

$base_dn = getenv('LDAP_BASE_DN') ?:
	 (defined('LDAP_BASE_DN') ? LDAP_BASE_DN : 'dc=example,dc=org');

return [
	'staff' => [
		'people' => [
			'type' => 'LDAP',
			'host' => getenv('LDAP_HOST') ?: (defined('LDAP_HOST') ? LDAP_HOST : 'localhost'),
			'port' => getenv('LDAP_PORT') ?: (defined('LDAP_PORT') ? LDAP_PORT : 389),
			'base_dn' => $base_dn,
			'bind_format' => defined('LDAP_BIND_FORMAT') ? LDAP_BIND_FORMAT : ('uid=%s,ou=people,'.$base_dn),
		]
		,
	]
];
