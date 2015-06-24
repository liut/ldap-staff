<?PHP

/**
 * undocumented class
 *
 * @package web
 * @author liut
 **/
class Controller_Profile extends Controller
{
	public function action_index($value='')
	{
		$current_user = $this->getUser();
		if (!$current_user->isLogin()) {
			return [302, '/sign/in'];
		}

		return ['staff/profile', [
			//
		]];
		// TODO:
	}

	public function action_settings_post()
	{
		// TODO:
	}
} // END class Controller_Profile
