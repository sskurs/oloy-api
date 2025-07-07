<?php

namespace OpenLoyalty\Bundle\UserBundle\Controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenLoyalty\Bundle\UserBundle\Service\CustomerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Debug controller for JWT authentication issues.
 */
class AuthDebugController extends Controller
{
    /**
     * @Route("/api/debug/jwt", name="debug_jwt", methods={"POST"})
     */
    public function debugJwtAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        
        if (!$token) {
            return new JsonResponse(['error' => 'No token provided'], 400);
        }

        $jwtSecret = $this->getParameter('jwt_secret');
        $jwtAlgorithm = $this->getParameter('jwt_algorithm');

        try {
            $payload = JWT::decode($token, new Key($jwtSecret, $jwtAlgorithm));
            
            /** @var CustomerProvider $customerProvider */
            $customerProvider = $this->get('oloy.user.customer_provider');
            
            $user = null;
            $userError = null;
            
            try {
                $user = $customerProvider->loadUserByUsername($payload->username);
            } catch (\Exception $e) {
                $userError = $e->getMessage();
            }

            return new JsonResponse([
                'token_decoded' => true,
                'payload' => $payload,
                'user_found' => $user !== null,
                'user_error' => $userError,
                'user_data' => $user ? [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'isActive' => $user->getIsActive(),
                    'roles' => $user->getRoles(),
                ] : null,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'token_decoded' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @Route("/api/debug/users", name="debug_users", methods={"GET"})
     */
    public function debugUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        // Check for customers
        $customers = $em->createQueryBuilder()
            ->select('c')
            ->from('OpenLoyaltyUserBundle:Customer', 'c')
            ->where('c.isActive = :active')
            ->setParameter('active', true)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $customerData = [];
        foreach ($customers as $customer) {
            $customerData[] = [
                'id' => $customer->getId(),
                'username' => $customer->getUsername(),
                'email' => $customer->getEmail(),
                'isActive' => $customer->getIsActive(),
            ];
        }

        return new JsonResponse([
            'total_customers' => count($customers),
            'customers' => $customerData,
        ]);
    }
} 