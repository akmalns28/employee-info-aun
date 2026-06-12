<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public $user;

    public function checkPermission($permission, $role = null)
    {
        $this->user = auth()->check() ? auth()->user() : null;
        // dd($this->user);
        if (is_null($this->user) || !$this->user->can($permission) || ($role && !$this->user->hasAnyRole((array) $role))) {
            abort(403, 'Sorry !! You are Unauthorized to access this resource.');
        }
    }

}
