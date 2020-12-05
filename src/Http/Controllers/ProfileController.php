<?php

namespace Umomega\Auth\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Auth\Http\Requests\UpdateProfile;
use Umomega\Auth\Http\Requests\UpdateProfilePassword;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{

	/**
	 * Returns User Profile Information
	 *
	 * @return json
	 */
	public function get()
	{
		return auth()->user()->only(['first_name', 'last_name', 'email', 'locale']);
	}

	/**
	 * Updates User Profile Information
	 *
	 * @param UpdateProfile
	 * @return json
	 */
	public function put(UpdateProfile $request)
	{
		$validated = $request->validated();

		auth()->user()->update($validated);

		activity()->log('ProfileUpdated');

		return ['message' => __('auth::users.updated_profile')];
	}

	/**
	 * Updates Password
	 *
	 * @param UpdateProfilePassword $request
	 * @return json
	 */
	public function password(UpdateProfilePassword $request)
	{
		$validated = $request->validated();
		$user = auth()->user();

		$user->setAttribute('password', bcrypt($validated['password']))->save();

		activity()->log('ProfileUpdatedPassword');

		return ['message' => __('auth::users.changed_password')];
	}

	/**
	 * Returns User Profile Information For the Store
	 *
	 * @return json
	 */
	public function info()
	{
		return auth()->user()->only(['first_name', 'last_name', 'email', 'full_name', 'initials', 'gravatar', 'all_permissions']);
	}

	/**
	 * Lists the recent activity by current logged user
	 *
	 * @return json
	 */
	public function chronicle()
	{
		return Activity::with('subject', 'causer')->where('causer_id', auth()->user()->id)->limit(30)->skip(request('s', 0))->latest()->get();
	}

}