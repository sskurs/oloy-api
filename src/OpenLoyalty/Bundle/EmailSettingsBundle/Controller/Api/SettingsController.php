<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailSettingsBundle\Controller\Api;

use OpenLoyalty\Bundle\EmailSettingsBundle\Form\Type\EmailFormType;
use OpenLoyalty\Domain\Email\Command\UpdateEmail;
use OpenLoyalty\Domain\Email\Email;
use OpenLoyalty\Domain\Email\EmailId;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsController.
 */
class SettingsController extends AbstractFOSRestController
{
    /**
     * Method will return complete list of available email settings.
     *
     * @Route(name="oloy.email_settings.list", path="/settings/emails")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction()
    {
        $emailRepository = $this->get('oloy.email.read_model.repository');
        $emails = $emailRepository->getAll();

        return $this->view(
            [
                'emails' => $emails,
                'total' => count($emails),
            ],
            200
        );
    }

    /**
     * Method will return details of particular email setting.
     *
     * @Route(name="oloy.email_settings.get", path="/settings/emails/{emailId}")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(Request $request)
    {
        $emailRepository = $this->get('oloy.email.read_model.repository');

        try {
            $emailEntity = $emailRepository->getById(new EmailId($request->get('emailId')));
        } catch (\Exception $e) {
            return $this->view(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->view(
            [
                'entity' => $emailEntity,
                'additional' => $this->get('oloy.email.settings')->getAdditionalParams($emailEntity),
            ]
        );
    }

    /**
     * @Route(name="oloy.email_settings.update", path="/settings/emails/{email}")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Email   $email
     *
     * @return \FOS\RestBundle\View\View
     */
    public function updateAction(Request $request, Email $email)
    {
        $form = $this->get('form.factory')->createNamed('email', EmailFormType::class, null, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $command = new UpdateEmail($email->getEmailId(), $data);
            $commandBus = $this->get('broadway.command_handling.command_bus');
            $commandBus->dispatch($command);

            return $this->view($email->getEmailId());
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }
}
