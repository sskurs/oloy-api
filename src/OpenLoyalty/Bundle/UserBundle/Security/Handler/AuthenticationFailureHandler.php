<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Handler;

use OpenLoyalty\Bundle\UserBundle\Exception\SellerIsNotActiveException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Custom Authentication Failure Handler.
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = 'Authentication failed';
        $statusCode = Response::HTTP_UNAUTHORIZED;

        if ($exception->getPrevious() instanceof SellerIsNotActiveException) {
            $message = $exception->getMessage();
            $statusCode = Response::HTTP_BAD_REQUEST;
        } elseif ($exception->getMessage()) {
            $message = $exception->getMessage();
        }

        return new JsonResponse([
            'message' => $message,
            'error' => 'authentication_failed'
        ], $statusCode);
    }
} 