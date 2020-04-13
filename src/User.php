<?php

namespace Umomega\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Umomega\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class User extends Authenticatable implements Searchable
{
    use Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'homepage_node'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['gravatar', 'full_name', 'initials', 'roles_list', 'permissions_list'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Searchable config
     *
     * @return SearchResult
     */
    public function getSearchResult(): SearchResult
    {
        return new SearchResult($this, $this->full_name);
    }

    /**
    * Send the password reset notification.
    *
    * @param  string  $token
    * @return void
    */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Getter for the full name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }

    /**
     * Getter user's initials
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        return substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
    }

    /**
     * Getter for the Gravatar URL
     *
     * @return string
     */
    public function getGravatarAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?d=blank';
    }

    /**
     * Getter for all permissions
     *
     * @return array
     */
    public function getAllPermissionsAttribute()
    {
        return $this->getAllPermissions()->pluck('name');
    }

    /**
     * Getter for roles
     *
     * @return array
     */
    public function getRolesListAttribute()
    {
        return $this->roles()->get()->map(function($role) {
            return ['name' => $role->name, 'id' => $role->id];
        });
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

    /**
     * Updates user's permissions
     *
     * @param array $roles
     * @param array $permissions
     */
    public function updatePermissions(array $roles, array $permissions) {
        $this->syncRoles(collect($roles)->pluck('name'));
        $this->syncPermissions(collect($permissions)->pluck('name'));
    }

    /**
     * Update user's API Token
     *
     * @param null|boolean $save
     */
    public function updateApiToken($save = true)
    {
        $this->api_token = \Illuminate\Support\Str::random(60);
        if($save) $this->save();
    }
    
}
