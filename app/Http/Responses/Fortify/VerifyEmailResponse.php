<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    use RedirectAfterAuth;

    public function toResponse($request)
    {
        $path = $this->homePath($request->user()).'?verified=1';

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($path);
    }
}
