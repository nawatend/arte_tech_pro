<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * @method render(string $string, array $array)
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        // ...
        //dd($accessDeniedException);


        $message = $accessDeniedException->getMessage() . "Not Admin";
        $status = $accessDeniedException->getCode();
        //return new Response($message, $status);
        return new RedirectResponse('/error');

    }
}