<?PHP

/**
 * undocumented class
 *
 * @package web
 * @author liut
 **/
class Controller_Home extends Controller
{
	public function action_index($value='')
	{
		$current_user = Staff::current();

		return ['staff/home', [
			'current_user' => $current_user
		]];
	}

	public function action_pi()
	{
		// $ldap = Da_Wrapper::dbo('staff.people');

		// echo getenv('LDAP_BASE_DN');
		// phpinfo(16+32);
		phpinfo();
	}
} // END class Controller_Home
