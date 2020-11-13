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
		$permission = Permission::create(array_merge($request->validated(), ['guard_name' => 'web']));

		activity()->on($permission)->log('PermissionStored');

		return [
			'message' => __('auth::permissions.created'),
			'payload' => $permission
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

		activity()->on($permission)->log('PermissionUpdated');

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

		activity()->on($permission)->withProperties(['user' => $user->full_name])->log('PermissionRevoked');

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
		
		$names = Permission::whereIn('id', $items)->pluck('name')->toArray();
		
		Permission::whereIn('id', $items)->delete();

		activity()->withProperties(compact('names'))->log('PermissionsDestroyedBulk');

		return ['message' => __('auth::permissions.deleted_multiple')];
	}

	/**
	 * Deletes a permission
	 *
	 * @param Permission $permission
	 * @return json
	 */
	public function destroy(Permission $permission)
	{
		$name = $permission->name;

		$permission->delete();

		activity()->withProperties(compact('name'))->log('PermissionDestroyed');

		return ['message' => __('auth::permissions.deleted')];
	}

}