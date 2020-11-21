<?php

namespace Umomega\Auth\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Auth\Role;
use Umomega\Auth\User;
use Umomega\Auth\Http\Requests\StoreRole;
use Umomega\Auth\Http\Requests\UpdateRole;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class RolesController extends Controller
{

	/**
	 * Returns a list of roles
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		return Role::orderBy($request->get('s', 'name'), $request->get('d', 'asc'))->paginate();
	}

	/**
	 * Returns a list of roles filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(Role::class, 'name')
			->search($request->get('q'))
			->map(function($role) {
				return $role->searchable;
			})];
	}

	/**
	 * Stores the new role
	 *
	 * @param StoreRole $request
	 * @return json
	 */
	public function store(StoreRole $request)
	{
		$validated = $request->validated();

		$role = Role::create(array_merge($validated, ['guard_name' => 'web']));

		$role->syncPermissions(collect($validated['permissions_list'])->pluck('name'));

		activity()->on($role)->log('RoleStored');

		return [
			'message' => __('auth::roles.created'),
			'payload' => $role
		];
	}

	/**
	 * Retrieves the role information
	 *
	 * @param Role $role
	 * @return json
	 */
	public function show(Role $role)
	{
		return $role;
	}

	/**
	 * Updates the role
	 *
	 * @param UpdateRole $request
	 * @param Role $role
	 * @return json
	 */
	public function update(UpdateRole $request, Role $role)
	{
		$validated = $request->validated();

		$role->update($validated);

		$role->syncPermissions(collect($validated['permissions_list'])->pluck('name'));

		activity()->on($role)->log('RoleUpdated');

		return [
			'message' => __('auth::roles.edited'),
			'payload' => $role,
			'event' => 'user-updated'
		];
	}

	/**
	 * Retrieves users associated to the role
	 *
	 * @param Request $request
	 * @param Role $role
	 * @return json
	 */
	public function users(Request $request, Role $role)
	{
		return $role->users()->orderBy($request->get('s', 'first_name'), $request->get('d', 'asc'))->paginate();
	}

	/**
	 * Revokes the role from the user
	 *
	 * @param Role $role
	 * @param User $user
	 * @return json
	 */
	public function revoke(Role $role, User $user)
	{
		$user->removeRole($role);

		activity()->on($role)->withProperties(['user' => $user->full_name])->log('RoleRevoked');

		$response = ['message' => __('auth::roles.revoked')];

		if($user->id == auth()->user()->id) $response['event'] = 'user-updated';

		return $response;
	}

	/**
	 * Bulk deletes roles
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];

		$names = Role::whereIn('id', $items)->pluck('name')->toArray();

		Role::whereIn('id', $items)->delete();

		activity()->withProperties(compact('names'))->log('RolesDestroyedBulk');

		return ['message' => __('auth::roles.deleted_multiple')];
	}

	/**
	 * Deletes a role
	 *
	 * @param Role $role
	 * @return json
	 */
	public function destroy(Role $role)
	{
		$name = $role->name;

		$role->delete();

		activity()->withProperties(compact('name'))->log('RoleDestroyed');

		return ['message' => __('auth::roles.deleted')];
	}

}