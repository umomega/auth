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

		return [
			'message' => __('auth::roles.edited'),
			'payload' => $role
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

		return ['message' => __('auth::roles.revoked')];
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

		Role::whereIn('id', $items)->delete();

		return ['message' => __('auth::roles.deleted_multiple')];
	}

	/**
	 * Deletes a role
	 *
	 * @param Request $request
	 * @param Role $role
	 * @return json
	 */
	public function destroy(Request $request, Role $role)
	{
		$role->delete();

		return ['message' => __('auth::roles.deleted')];
	}

}