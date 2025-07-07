<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Controller\Api;

use Broadway\CommandHandling\SimpleCommandBus;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\AdminCommand;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\CreateAdmin;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\EditAdmin;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\SelfEditAdmin;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\Repository\AdminRepository;
use OpenLoyalty\Bundle\UserBundle\Exception\EmailAlreadyExistException;
use OpenLoyalty\Bundle\UserBundle\Form\Type\AdminFormType;
use OpenLoyalty\Bundle\UserBundle\Form\Type\AdminSelfEditFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController.
 */
class AdminController extends AbstractFOSRestController
{
    /**
     * List all admins.
     *
     * @Route(name="oloy.user.list", path="/admin")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function listAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'lastName', 'asc');

        /** @var AdminRepository $repo */
        $repo = $this->get('oloy.user.repository.admin');
        $users = $repo->findAllPaginated(
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $repo->countTotal();

        return $this->view([
            'users' => $users,
            'total' => $total,
        ], 200);
    }

    /**
     * Method allows to update admin data.
     *
     * @param Request    $request
     * @param Admin|null $admin
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.edit_admin", path="/admin/data/{admin}")
     *
     * @Method("PUT")
     */
    public function editAction(Request $request, Admin $admin = null)
    {
        if (!$admin) {
            $admin = $this->getUser();
        }
        $this->denyAccessUnlessGranted('EDIT', $admin);

        if ($admin->getId() == $this->getUser()->getId()) {
            $type = AdminSelfEditFormType::class;
            $command = new SelfEditAdmin($admin);
        } else {
            $type = AdminFormType::class;
            $command = new EditAdmin($admin);
        }

        $form = $this->get('form.factory')->createNamed('admin', $type, $command, [
            'method' => 'PUT',
            'validation_groups' => function (FormInterface $form) {
                if (!$form->get('external')->getData()) {
                    return ['Default', 'internal'];
                } else {
                    return ['Default', 'external'];
                }
            },
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->handleAdminCommand($command, $form);
        }

        return $this->view($form->getErrors(), 400);
    }

    /**
     * Method allows to create new admin.
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.create_admin", path="/admin/data")
     *
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('CREATE_USER');
        $command = new CreateAdmin();
        $form = $this->get('form.factory')->createNamed('admin', AdminFormType::class, $command, [
            'method' => 'POST',
            'validation_groups' => function (FormInterface $form) {
                if (!$form->get('external')->getData()) {
                    return ['Default', 'internal'];
                } else {
                    return ['Default', 'external'];
                }
            },
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->handleAdminCommand($command, $form);
        }

        return $this->view($form->getErrors(), 400);
    }

    /**
     * Method will return admin details.
     *
     * @param Admin|null $admin
     *
     * @return \FOS\RestBundle\View\View
     * @Route(name="oloy.user.get_admin", path="/admin/data/{admin}")
     *
     * @Method("GET")
     */
    public function getAction(Admin $admin = null)
    {
        /* @var Admin $user */
        if (!$admin) {
            $admin = $this->getUser();
        }

        $this->denyAccessUnlessGranted('VIEW', $admin);

        return $this->view($admin, 200);
    }

    protected function handleAdminCommand(AdminCommand $command, FormInterface $form)
    {
        /** @var SimpleCommandBus $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');
        try {
            $commandBus->dispatch($command);
        } catch (EmailAlreadyExistException $e) {
            $form->get('email')->addError(new FormError($e->getMessage()));

            return $this->view($form->getErrors(), 400);
        } catch (\DomainException $e) {
            $form->addError(new FormError($e->getMessage()));

            return $this->view($form->getErrors(), 400);
        }

        return $this->view(null, 200);
    }
}
