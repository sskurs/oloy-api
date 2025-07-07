<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use OpenLoyalty\Bundle\AuditBundle\Service\AuditManagerInterface;
use OpenLoyalty\Bundle\UserBundle\Entity\Customer;
use OpenLoyalty\Bundle\UserBundle\Entity\Seller;
use OpenLoyalty\Bundle\UserBundle\Entity\User;
use OpenLoyalty\Bundle\UserBundle\Event\UserRegisteredWithInvitationToken;
use OpenLoyalty\Bundle\UserBundle\Form\Type\CustomerEditFormType;
use OpenLoyalty\Bundle\UserBundle\Form\Type\CustomerRegistrationFormType;
use OpenLoyalty\Bundle\UserBundle\Form\Type\CustomerSelfRegistrationFormType;
use OpenLoyalty\Domain\Customer\Command\ActivateCustomer;
use OpenLoyalty\Domain\Customer\Command\AssignPosToCustomer;
use OpenLoyalty\Domain\Customer\Command\DeactivateCustomer;
use OpenLoyalty\Domain\Customer\Command\MoveCustomerToLevel;
use OpenLoyalty\Domain\Customer\Command\NewsletterSubscription;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\PosId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomers;
use OpenLoyalty\Domain\Segment\ReadModel\SegmentedCustomersRepository;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Seller\SellerId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CustomerController.
 */
class CustomerController extends AbstractFOSRestController
{
    /**
     * Method will return list of all customers.
     *
     * @Route(name="oloy.customer.list", path="/customer")
     * @Method("GET")
     * @Security("is_granted('LIST_CUSTOMERS')")
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return \FOS\RestBundle\View\View
     *
     * @QueryParam(name="firstName", nullable=true, description="firstName"))
     * @QueryParam(name="lastName", nullable=true, description="lastName"))
     * @QueryParam(name="phone", nullable=true, description="phone"))
     * @QueryParam(name="email", nullable=true, description="email"))
     * @QueryParam(name="loyaltyCardNumber", nullable=true, description="loyaltyCardNumber"))
     * @QueryParam(name="transactionsAmount", nullable=true, description="transactionsAmount"))
     * @QueryParam(name="averageTransactionAmount", nullable=true, description="averageTransactionAmount"))
     * @QueryParam(name="transactionsCount", nullable=true, description="transactionsCount"))
     * @QueryParam(name="daysFromLastTransaction", nullable=true, description="daysFromLastTransaction"))
     * @QueryParam(name="hoursFromLastUpdate", nullable=true, description="hoursFromLastUpdate"))
     */
    public function listAction(Request $request, ParamFetcher $paramFetcher)
    {
        $types = [
            'transactionsAmount' => 'number',
            'averageTransactionAmount' => 'number',
            'transactionsCount' => 'number',
        ];

        $params = $this->get('oloy.user.param_manager')->stripNulls($paramFetcher->all(), true, true, $types);
        if (isset($params['daysFromLastTransaction'])) {
            $days = $params['daysFromLastTransaction'];
            unset($params['daysFromLastTransaction']);
            $params['lastTransactionDate'] = [
                'type' => 'range',
                'value' => [
                    'gte' => (new \DateTime('-'.$days.' days'))->getTimestamp(),
                ],
            ];
        }
        if (isset($params['hoursFromLastUpdate'])) {
            $hoursFromLastUpdate = $params['hoursFromLastUpdate'];
            unset($params['hoursFromLastUpdate']);
            $params['updatedAt'] = [
                'type' => 'range',
                'value' => [
                    'gte' => (new \DateTime('-'.$hoursFromLastUpdate.' hours'))->getTimestamp(),
                ],
            ];
        }
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'createdAt', 'desc');

        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $customers = $repo->findByParametersPaginated(
            $params,
            $request->get('strict', false),
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal($params, $request->get('strict', false));

        return $this->view(
            [
                'customers' => $customers,
                'total' => $total,
            ],
            200
        );
    }

    /**
     * Method will return customer details.
     *
     * @Route(name="oloy.customer.get", path="/customer/{customer}")
     * @Method("GET")
     * @Security("is_granted('VIEW', customer)")
     *
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomerAction(CustomerDetails $customer)
    {
        $view = $this->view(
            $customer,
            200
        );
        /** @var SegmentedCustomersRepository $repo */
        $repo = $this->get('oloy.segment.read_model.repository.segmented_customers');
        $segments = $repo->findBy(['customerId' => $customer->getCustomerId()->__toString()]);
        $serializer = $this->get('serializer');
        $segments = array_map(
            function (SegmentedCustomers $segment) use ($serializer) {
                return $serializer->toArray($segment);
            },
            $segments
        );

        $auditManager = $this->container->get('oloy.audit.manager');
        $auditManager->auditCustomerEvent(AuditManagerInterface::VIEW_CUSTOMER_EVENT_TYPE, $customer->getCustomerId());

        $context = new Context();
        $context->addGroup('Default');
        $context->setAttribute('customerSegments', $segments);

        $view->setContext($context);

        return $view;
    }

    /**
     * Method will return number of customer registrations per day in last 30 days.
     *
     * @Route(name="oloy.customer.get_customers_registrations_in_time", path="/customer/registrations/daily")
     * @Method("GET")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomersRegistrationsDailyAction()
    {
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $date->modify('-30 days');
        $customers = $repo->findByParameters(
            [
                'createdAt' => [
                    'type' => 'range',
                    'value' => [
                        'gte' => $date->getTimestamp(),
                    ],
                ],
            ]
        );

        $result = [];
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        while ($date < $now) {
            $result[$date->format('Y-m-d')] = 0;
            $date->modify('+1 day');
        }

        /** @var CustomerDetails $customer */
        foreach ($customers as $customer) {
            $tmp = $customer->getCreatedAt()->format('Y-m-d');
            if (!isset($result[$tmp])) {
                continue;
            }
            $result[$tmp] += 1;
        }

        return $this->view($result);
    }

    /**
     * Method will return customer status<br/>
     * [Example response]<br/>
     * <pre>.
     *
     {
     "firstName": "Jane",
     "lastName": "Doe",
     "customerId": "00000000-0000-474c-b092-b0dd880c07e2",
     "points": 206,
     "usedPoints": 100,
     "expiredPoints": 0,
     "level": "14.00%",
     "levelName": "level0",
     "nextLevel": "15.00%",
     "nextLevelName": "level1",
     "transactionsAmountWithoutDeliveryCosts": 3,
     "transactionsAmountToNextLevel": 17,
     "averageTransactionsAmount": "3.00",
     "transactionsCount": 1,
     "transactionsAmount": 3,
     "currency": "eur",
     }
     * </pre>
     *
     * @Route(name="oloy.customer.get_status", path="/customer/{customer}/status")
     * @Route(name="oloy.customer.admin_get_status", path="/admin/customer/{customer}/status")
     * @Route(name="oloy.customer.seller_get_status", path="/seller/customer/{customer}/status")
     * @Method("GET")
     * @Security("is_granted('VIEW_STATUS', customer)")
     *
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomerStatusAction(CustomerDetails $customer)
    {
        return $this->view(
            $this->get('oloy.customer_status_provider')->getStatus($customer->getCustomerId()),
            200
        );
    }

    /**
     * Method allows to register new customer.
     *
     * @param Request $request
     * @Route(name="oloy.customer.register_customer", path="/customer/register")
     * @Route(name="oloy.customer.seller.register_customer", path="/seller/customer/register")
     * @Security("is_granted('CREATE_CUSTOMER')")
     *
     * @Method("POST")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function registerCustomerAction(Request $request)
    {
        $loggedUser = $this->getUser();

        $formOptions = [];
        $formOptions['includeLevelId'] = true;
        $formOptions['includePosId'] = true;

        $form = $this->get('form.factory')->createNamed(
            'customer',
            CustomerRegistrationFormType::class,
            null,
            $formOptions
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $customerId = new CustomerId($this->get('broadway.uuid.generator')->generate());

            $user = $this->get('oloy.user.form_handler.customer_registration')->onSuccess($customerId, $form);

            if ($user instanceof User) {
                $levelId = $form->get('levelId')->getData();
                $posId = $form->get('posId')->getData();
                $agreement2 = $form->get('agreement2')->getData();
                $commandBus = $this->get('broadway.command_handling.command_bus');

                if (!$posId && $this->isGranted('ROLE_SELLER') && $loggedUser instanceof Seller) {
                    $this->handleSellerWasACreator($loggedUser, $customerId, $user);
                } elseif ($posId) {
                    $commandBus->dispatch(
                        new AssignPosToCustomer($customerId, new PosId($posId))
                    );
                }
                if ($levelId) {
                    $commandBus->dispatch(
                        new MoveCustomerToLevel($customerId, new LevelId($levelId), true)
                    );
                }

                $commandBus->dispatch(
                    new ActivateCustomer($customerId)
                );

                if ($agreement2) {
                    $this->dispatchNewsletterSubscriptionEvent($user, $customerId);
                }

                return $this->view(
                    [
                        'customerId' => $customerId->__toString(),
                        'email' => $user->getEmail(),
                    ]
                );
            }

            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allow to register by myself.
     *
     * @param Request $request
     * @Route(name="oloy.customer.self_register_customer", path="/customer/self_register")
     *
     * @Method("POST")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function selfRegisterAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('customer', CustomerSelfRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $customerId = new CustomerId($this->get('broadway.uuid.generator')->generate());

            $user = $this->get('oloy.user.form_handler.customer_registration')->onSuccess($customerId, $form);

            if ($user instanceof User) {
                $referralCustomerEmail = $form->get('referral_customer_email')->getData();

                $this->handleCustomerRegisteredByHimself($user, $referralCustomerEmail);

                if ($invitationToken = $form->get('invitationToken')->getData()) {
                    $this->get('event_dispatcher')->dispatch(
                        UserRegisteredWithInvitationToken::NAME,
                        new UserRegisteredWithInvitationToken($invitationToken, $customerId)
                    );
                }

                return $this->view(
                    [
                        'customerId' => $customerId->__toString(),
                        'email' => $user->getEmail(),
                    ]
                );
            }

            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to update customer details.
     *
     * @param Request         $request
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.customer.edit_customer", path="/customer/{customer}")
     * @Security("is_granted('EDIT', customer)")
     *
     * @Method("PUT")
     */
    public function editCustomerAction(Request $request, CustomerDetails $customer)
    {
        $form = $this->get('form.factory')->createNamed(
            'customer',
            CustomerEditFormType::class,
            [],
            [
                'method' => 'PUT',
                'includeLevelId' => true,
                'includePosId' => true,
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $ret = $this->get('oloy.user.form_handler.customer_edit')->onSuccess($customer->getCustomerId(), $form);

            if ($ret !== true) {
                return $this->view($ret, Response::HTTP_BAD_REQUEST);
            }

            $levelId = $form->get('levelId')->getData();
            $posId = $form->get('posId')->getData();
            $commandBus = $this->get('broadway.command_handling.command_bus');
            if ($posId) {
                $commandBus->dispatch(
                    new AssignPosToCustomer($customer->getCustomerId(), new PosId($posId))
                );
            }
            if ($levelId) {
                $commandBus->dispatch(
                    new MoveCustomerToLevel($customer->getCustomerId(), new LevelId($levelId), true)
                );
            }

            /** @var CustomerDetailsRepository $repo */
            $repo = $this->get('oloy.user.read_model.repository.customer_details');
            $customer = $repo->find($customer->getCustomerId()->__toString());

            if ($customer->isAgreement2()) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('OpenLoyaltyUserBundle:Customer')->findOneBy(['id' => $customer->getId()]);
                $this->dispatchNewsletterSubscriptionEvent($user, $customer->getCustomerId());
            }

            return $this->view($customer);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to assign level to customer.
     *
     * @param Request         $request
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.customer.add_customer_to_level", path="/customer/{customer}/level")
     * @Method("POST")
     * @Security("is_granted('ADD_TO_LEVEL', customer)")
     */
    public function addCustomerToLevelAction(Request $request, CustomerDetails $customer)
    {
        $levelId = $request->request->get('levelId');
        if (!$levelId) {
            return $this->view(['levelId' => 'field is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->get('broadway.command_handling.command_bus')->dispatch(
            new MoveCustomerToLevel($customer->getCustomerId(), new LevelId($levelId), true)
        );

        return $this->view([]);
    }

    /**
     * Method allows to assign POS to customer.
     *
     * @param Request         $request
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.customer.assign_pos", path="/customer/{customer}/pos")
     * @Route(name="oloy.customer.seller.assign_pos", path="/seller/customer/{customer}/pos")
     * @Method("POST")
     * @Security("is_granted('ASSIGN_POS', customer)")
     */
    public function assignPosToCustomerAction(Request $request, CustomerDetails $customer)
    {
        $posId = $request->request->get('posId');
        if (!$posId) {
            return $this->view(['posId' => 'field is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->get('broadway.command_handling.command_bus')->dispatch(
            new AssignPosToCustomer($customer->getCustomerId(), new PosId($posId))
        );

        return $this->view([]);
    }

    /**
     * Method allows to deactivate customer<br/>Inactive customer will not be able to log in.
     *
     * @Route(name="oloy.customer.deactivate_customer", path="/admin/customer/{customer}/deactivate")
     * @Route(name="oloy.customer.seller.deactivate_customer", path="/seller/customer/{customer}/deactivate")
     * @Method("POST")
     * @Security("is_granted('DEACTIVATE', customer)")
     *
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deactivateCustomerAction(CustomerDetails $customer)
    {
        $this->get('broadway.command_handling.command_bus')->dispatch(
            new DeactivateCustomer($customer->getCustomerId())
        );

        $user = $this->getDoctrine()->getManager()->find(Customer::class, $customer->getCustomerId()->__toString());
        if ($user instanceof User) {
            $user->setIsActive(false);
            $this->get('oloy.user.user_manager')->updateUser($user);
        }

        return $this->view('');
    }

    /**
     * Method allows to activate customer.
     *
     * @Route(name="oloy.customer.ativate_customer", path="/admin/customer/{customer}/activate")
     * @Route(name="oloy.customer.seller.activate_customer", path="/seller/customer/{customer}/activate")
     * @Method("POST")
     * @Security("is_granted('ACTIVATE', customer)")
     *
     * @param CustomerDetails $customer
     *
     * @return \FOS\RestBundle\View\View
     */
    public function activateCustomerAction(CustomerDetails $customer)
    {
        $this->get('broadway.command_handling.command_bus')->dispatch(
            new ActivateCustomer($customer->getCustomerId())
        );

        $user = $this->getDoctrine()->getManager()->find(Customer::class, $customer->getCustomerId()->__toString());
        if ($user instanceof User) {
            $user->setIsActive(true);
            $this->get('oloy.user.user_manager')->updateUser($user);
        }

        return $this->view('');
    }

    /**
     * Method allows to activate by activation token.
     *
     * @Route(name="oloy.customer.ativate_account", path="/customer/activate/{token}")
     * @Method("POST")
     *
     * @param $token
     *
     * @return \FOS\RestBundle\View\View
     */
    public function activateAccountAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OpenLoyaltyUserBundle:Customer')->findOneBy(['actionToken' => $token]);

        if ($user instanceof Customer && $token == $user->getActionToken()) {
            $user->setIsActive(true);
            $commandBus = $this->get('broadway.command_handling.command_bus');
            $commandBus->dispatch(
                new ActivateCustomer(new CustomerId($user->getId()))
            );
            $this->get('oloy.user.user_manager')->updateUser($user);

            $customerId = new CustomerId($user->getId());

            $repo = $this->get('oloy.user.read_model.repository.customer_details');
            $customer = $repo->find($user->getId());
            if ($customer->isAgreement2()) {
                $this->dispatchNewsletterSubscriptionEvent($user, $customerId);
            }

            return $this->view('', 200);
        } else {
            throw new NotFoundHttpException('bad_token');
        }
    }

    /**
     * Get the currently authenticated customer details.
     *
     * @Route(name="oloy.customer.me", path="/customer/me")
     * @Method("GET")
     * @Security("is_granted('ROLE_PARTICIPANT')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function meAction()
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof \OpenLoyalty\Bundle\UserBundle\Entity\Customer) {
            return $this->view(['error' => 'Not authenticated as customer'], 401);
        }
        $customerId = $user->getId();
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $customer = $repo->find($customerId);
        if (!$customer) {
            return $this->view(['error' => 'Customer not found'], 404);
        }
        return $this->view($customer, 200);
    }

    /**
     * @param SellerId $id
     *
     * @return SellerDetails|null
     */
    protected function getSellerDetails(SellerId $id)
    {
        /** @var SellerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.seller_details');

        return $repo->find($id->__toString());
    }

    protected function handleSellerWasACreator(User $loggedUser, $customerId, User $user)
    {
        $sellerDetails = $this->getSellerDetails(new SellerId($loggedUser->getId()));
        if ($sellerDetails instanceof SellerDetails && $sellerDetails->getPosId()) {
            // assign pos and send email
            $this->get('broadway.command_handling.command_bus')->dispatch(
                new AssignPosToCustomer($customerId, new PosId($sellerDetails->getPosId()->__toString()))
            );
        }
    }

    protected function handleCustomerRegisteredByHimself(User $user, $referralCustomerEmail)
    {
        $user->setIsActive(false);
        if ($user instanceof Customer) {
            $user->setActionToken(substr(md5(uniqid(null, true)), 0, 20));
            $user->setReferralCustomerEmail($referralCustomerEmail);
            $url = $this->container->getParameter('frontend_activate_account_url').'/'.$user->getActionToken();
        }
        $this->getDoctrine()->getManager()->flush();

        $this->get('oloy.user.email_provider')->registration(
            $user,
            isset($url) ? $url : null
        );
    }

    /**
     * @param User       $user
     * @param CustomerId $customerId
     */
    protected function dispatchNewsletterSubscriptionEvent(User $user, CustomerId $customerId)
    {
        if ($user instanceof User && !$user->getNewsletterUsedFlag()) {
            $user->setNewsletterUsedFlag(true);
            $this->getDoctrine()->getManager()->flush();

            $commandBus = $this->get('broadway.command_handling.command_bus');
            $commandBus->dispatch(
                new NewsletterSubscription($customerId)
            );
        }
    }
}
