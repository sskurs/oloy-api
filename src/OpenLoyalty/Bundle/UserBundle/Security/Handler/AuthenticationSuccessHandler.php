<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Handler;

use Firebase\JWT\JWT;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Service\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Custom Authentication Success Handler that generates JWT tokens.
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtSecret;
    private $jwtAlgorithm;
    private $jwtTtl;
    private $userManager;

    public function __construct(string $jwtSecret, string $jwtAlgorithm, int $jwtTtl, UserManager $userManager)
    {
        $this->jwtSecret = $jwtSecret;
        $this->jwtAlgorithm = $jwtAlgorithm;
        $this->jwtTtl = $jwtTtl;
        $this->userManager = $userManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
        
        if ($user instanceof User) {
            // Update last login time
            $user->setLastLoginAt(new \DateTime());
            $this->userManager->updateUser($user);
        }

        // Generate JWT token
        $payload = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'id' => $user->getId(),
            'iat' => time(),
            'exp' => time() + $this->jwtTtl,
        ];

        if ($user instanceof User) {
            $payload['lastLoginAt'] = $user->getLastLoginAt() ? $user->getLastLoginAt()->format(\DateTime::ISO8601) : null;
        }

        $jwtToken = JWT::encode($payload, $this->jwtSecret, $this->jwtAlgorithm);

        return new JsonResponse([
            'token' => $jwtToken,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
} 