<?php

namespace App\Policies;

use Sereny\NovaPermissions\Policies\BasePolicy;

class TipoPolicy extends BasePolicy
{
    public $key = 'Tipos';

    public function viewAny($user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::viewAny($user);
    }

    public function view($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::view($user, $model);
    }

    public function create($user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::create($user);
    }

    public function update($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::update($user, $model);
    }

    public function delete($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::delete($user, $model);
    }

    public function restore($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::restore($user, $model);
    }

    public function forceDelete($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::forceDelete($user, $model);
    }

    public function destroy($user, $model)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return parent::destroy($user, $model);
    }
}
