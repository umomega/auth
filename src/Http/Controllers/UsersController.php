<?php

namespace Umomega\Auth\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Auth\User;
use Umomega\Auth\Http\Requests\StoreUser;
use Umomega\Auth\Http\Requests\UpdateUser;
use Umomega\Auth\Http\Requests\UpdateUserPassword;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class UsersController extends Controller
{

	/**
	 * Returns a list of users
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		return User::orderBy($request->get('s', 'first_name'), $request->get('d', 'asc'))->paginate();
	}

	/**
	 * Returns a list of users filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(User::class, 'first_name', 'last_name', 'email')
			->search($request->get('q'))
			->map(function($user) {
				return $user->searchable;
			})];
	}

	/**
	 * Stores the new user
	 *
	 * @param StoreUser $request
	 * @return json
	 */
	public function store(StoreUser $request)
	{
		$validated = $request->validated();

		$user = new User($validated);
		$user->password = bcrypt($validated['password']);
		$user->updateApiToken(false);
		$user->save();

		$user->updatePermissions($validated['roles_list'], $validated['permissions_list']);

		activity()->on($user)->log('UserStored');

		return [
			'message' => __('auth::users.created'),
			'payload' => $user
		];
	}

	/**
	 * Retrieves the user information
	 *
	 * @param User $user
	 * @return json
	 */
	public function show(User $user)
	{
		return $user;
	}

	/**
	 * Updates the user
	 *
	 * @param UpdateUser $request
	 * @param User $user
	 * @return json
	 */
	public function update(UpdateUser $request, User $user)
	{
		$validated = $request->validated();
		$user->fill($validated);

		if(!empty($validated['password']))
		{
			$user->password = bcrypt($validated['password']);
		}

		$user->save();

		$user->updatePermissions($validated['roles_list'], $validated['permissions_list']);

		activity()->on($user)->log('UserUpdated');

		return [
			'message' => __('auth::users.edited'),
			'payload' => $user
		];
	}

	/**
	 * Updates User's Password
	 *
	 * @param UpdateUserPassword $request
	 * @param User $user
	 * @return json
	 */
	public function password(UpdateUserPassword $request, User $user)
	{
		$validated = $request->validated();

		$user->setAttribute('password', bcrypt($validated['password']))->save();

		activity()->on($user)->log('UserUpdatedPassword');

		return [
			'message' => __('auth::users.changed_password'),
			'payload' => $user
		];
	}

	/**
	 * Bulk deletes users
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];

		$names = User::whereIn('id', $items)->get()->pluck('full_name')->toArray();

		User::whereIn('id', $items)->delete();

		activity()->withProperties(compact('names'))->log('UsersDestroyedBulk');

		return ['message' => __('auth::users.deleted_multiple')];
	}

	/**
	 * Deletes a user
	 *
	 * @param Request $request
	 * @param User $user
	 * @return json
	 */
	public function destroy(Request $request, User $user)
	{
		$name = $user->full_name;

		$user->delete();

		activity()->withProperties(compact('name'))->log('UserDestroyed');

		return ['message' => __('auth::users.deleted')];
	}

}