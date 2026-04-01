<?php

namespace App\Http\Responses\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait RedirectAfterAuth
{
    protected function homePath(?Authenticatable $user): string
    {
        if ($user instanceof User && $user->is_admin) {
            return '/admin';
        }

        return '/';
    }
}
