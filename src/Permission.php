<?php

namespace Umomega\Auth;

use Spatie\Permission\Models\Permission as BasePermission;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Permission extends BasePermission implements Searchable {

	/**
	 * Searchable config
	 *
	 * @return SearchResult
	 */
	public function getSearchResult(): SearchResult
	{
		return new SearchResult($this, $this->name);
	}

}