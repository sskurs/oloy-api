<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Authenticator;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenLoyalty\Bundle\UserBundle\Service\AdminProvider;
use OpenLoyalty\Bundle\UserBundle\Service\CustomerProvider;
use OpenLoyalty\Bundle\UserBundle\Service\SellerProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Custom JWT Authenticator using Firebase JWT library.
 */
class JWTAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtSecret;
    private $jwtAlgorithm;

    public function __construct(string $jwtSecret, string $jwtAlgorithm = 'HS256')
    {
        $this->jwtSecret = $jwtSecret;
        $this->jwtAlgorithm = $jwtAlgorithm;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'message' => 'Authentication Required'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') &&
               strpos($request->headers->get('Authorization'), 'Bearer ') === 0;
    }

    public function getCredentials(Request $request)
    {
        $token = $this->extractToken($request);
        
        if (!$token) {
            return null;
        }

        return ['token' => $token];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!isset($credentials['token'])) {
            return null;
        }

        try {
            $payload = JWT::decode($credentials['token'], new Key($this->jwtSecret, $this->jwtAlgorithm));
            
            if (!isset($payload->username)) {
                return null;
            }

            $user = $userProvider->loadUserByUsername($payload->username);
            
            if (!$user) {
                // Log the issue for debugging
                error_log(sprintf('JWT Authenticator: User not found for username "%s"', $payload->username));
            }
            
            return $user;
        } catch (\Exception $e) {
            // Log the exception for debugging
            error_log(sprintf('JWT Authenticator Exception: %s', $e->getMessage()));
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function extractToken(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            return null;
        }

        return substr($authHeader, 7);
    }
} 