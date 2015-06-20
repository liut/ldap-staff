<?PHP

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class Controller_Home extends Controller
{
	public function action_index($value='')
	{
		return 'staff/home';
	}

	public function action_pi()
	{
		// $ldap = Da_Wrapper::dbo('staff.people');

		// echo getenv('LDAP_BASE_DN');
		// phpinfo(16+32);
		phpinfo();
	}
} // END class Controller_Home
