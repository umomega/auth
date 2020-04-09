<?php

namespace Umomega\Auth\Http\Controllers;

use Umomega\Foundation\Http\Controllers\Controller;
use Umomega\Auth\Permission;
use Umomega\Auth\User;
use Umomega\Auth\Http\Requests\StorePermission;
use Umomega\Auth\Http\Requests\UpdatePermission;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class PermissionsController extends Controller
{

	/**
	 * Returns a list of permissions
	 *
	 * @param Request $request
	 * @return json
	 */
	public function index(Request $request)
	{
		return Permission::orderBy($request->get('s', 'name'), $request->get('d', 'asc'))->paginate();
	}

	/**
	 * Returns a list of permissions filtered by search
	 *
	 * @param Request $request
	 * @return json
	 */
	public function search(Request $request)
	{
		return ['data' => (new Search())
			->registerModel(Permission::class, 'name')
			->search($request->get('q'))
			->map(function($permission) {
				return $permission->searchable;
			})];
	}

	/**
	 * Stores the new permission
	 *
	 * @param StorePermission $request
	 * @return json
	 */
	public function store(StorePermission $request)
	{
		return [
			'message' => __('auth::permissions.created'),
			'payload' => Permission::create(array_merge($request->validated(), ['guard_name' => 'web']))
		];
	}

	/**
	 * Retrieves the permission information
	 *
	 * @param Permission $permission
	 * @return json
	 */
	public function show(Permission $permission)
	{
		return $permission;
	}

	/**
	 * Updates the permission
	 *
	 * @param UpdatePermission $request
	 * @param Permission $permission
	 * @return json
	 */
	public function update(UpdatePermission $request, Permission $permission)
	{
		$permission->update($request->validated());

		return [
			'message' => __('auth::permissions.edited'),
			'payload' => $permission
		];
	}

	/**
	 * Retrieves users associated to the permission
	 *
	 * @param Request $request
	 * @param Permission $permission
	 * @return json
	 */
	public function users(Request $request, Permission $permission)
	{
		return $permission->users()->orderBy($request->get('s', 'first_name'), $request->get('d', 'asc'))->paginate();
	}

	/**
	 * Revokes the permission from the user
	 *
	 * @param Permission $permission
	 * @param User $user
	 * @return json
	 */
	public function revoke(Permission $permission, User $user)
	{
		$user->revokePermissionTo($permission);

		return ['message' => __('auth::permissions.revoked')];
	}


	/**
	 * Bulk deletes permissions
	 *
	 * @param Request $request
	 * @return json
	 */
	public function destroyBulk(Request $request)
	{
		$items = $this->validate($request, ['items' => 'required|array'])['items'];

		Permission::whereIn('id', $items)->delete();

		return ['message' => __('auth::permissions.deleted_multiple')];
	}

	/**
	 * Deletes a permissions
	 *
	 * @param Request $request
	 * @param Permission $permission
	 * @return json
	 */
	public function destroy(Request $request, Permission $permission)
	{
		$permission->delete();

		return ['message' => __('auth::permissions.deleted')];
	}

}