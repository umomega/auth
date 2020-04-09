<?php

namespace Umomega\Auth;

use Spatie\Permission\Models\Role as BaseRole;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Role extends BaseRole implements Searchable {

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'guard_name'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['permissions_list'];

	/**
	 * Searchable config
	 *
	 * @return SearchResult
	 */
	public function getSearchResult(): SearchResult
	{
		return new SearchResult($this, $this->name);
	}

	/**
     * Getter for permissions
     *
     * @return array
     */
    public function getPermissionsListAttribute()
    {
        return $this->permissions()->get()->map(function($permission) {
            return ['name' => $permission->name, 'id' => $permission->id];
        });
    }

}