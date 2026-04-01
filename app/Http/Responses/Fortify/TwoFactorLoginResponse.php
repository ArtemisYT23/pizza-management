<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    use RedirectAfterAuth;

    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($this->homePath($request->user()));
    }
}
