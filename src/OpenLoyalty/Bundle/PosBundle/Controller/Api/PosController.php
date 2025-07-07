<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\PosBundle\Controller\Api;

use Broadway\CommandHandling\CommandBusInterface;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenLoyalty\Bundle\PosBundle\Form\Type\CreatePosFormType;
use OpenLoyalty\Bundle\PosBundle\Form\Type\EditPosFormType;
use OpenLoyalty\Bundle\PosBundle\Model\Pos;
use OpenLoyalty\Domain\Pos\Command\CreatePos;
use OpenLoyalty\Domain\Pos\Command\UpdatePos;
use OpenLoyalty\Domain\Pos\PosId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PosController.
 */
class PosController extends AbstractFOSRestController
{
    /**
     * Method allows to create new POS.
     *
     * @Route(name="oloy.pos.create", path="/pos")
     * @Security("is_granted('CREATE_POS')")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('pos', CreatePosFormType::class);
        $uuidGenerator = $this->get('broadway.uuid.generator');

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Pos $data */
            $data = $form->getData();
            $id = new PosId($uuidGenerator->generate());

            $commandBus->dispatch(
                new CreatePos($id, $data->toArray())
            );

            return $this->view(['posId' => $id->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method allows to update POS data.
     *
     * @Route(name="oloy.pos.update", path="/pos/{pos}")
     * @Method("PUT")
     * @Security("is_granted('EDIT', pos)")
     *
     * @param Request                     $request
     * @param \OpenLoyalty\Domain\Pos\Pos $pos
     *
     * @return \FOS\RestBundle\View\View
     */
    public function updateAction(Request $request, \OpenLoyalty\Domain\Pos\Pos $pos)
    {
        $form = $this->get('form.factory')->createNamed('pos', EditPosFormType::class, null, [
            'method' => 'PUT',
        ]);

        /** @var CommandBusInterface $commandBus */
        $commandBus = $this->get('broadway.command_handling.command_bus');

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Pos $data */
            $data = $form->getData();

            $commandBus->dispatch(
                new UpdatePos($pos->getPosId(), $data->toArray())
            );

            return $this->view(['posId' => $pos->getPosId()->__toString()]);
        }

        return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Method will return POS details.
     *
     * @Route(name="oloy.pos.get", path="/pos/{pos}")
     * @Route(name="oloy.pos.seller.get", path="/seller/pos/{pos}")
     * @Method("GET")
     * @Security("is_granted('VIEW', pos)")
     *
     * @param \OpenLoyalty\Domain\Pos\Pos $pos
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(\OpenLoyalty\Domain\Pos\Pos $pos)
    {
        return $this->view($pos);
    }

    /**
     * Method will return POS details. <br/>
     * You need to provide POS identifier.
     *
     * @Route(name="oloy.pos.get_by_identifier", path="/pos/identifier/{pos}")
     * @Method("GET")
     * @Security("is_granted('VIEW', pos)")
     * @ParamConverter(class="OpenLoyalty\Domain\Pos\Pos", name="pos", options={"identifier":true})
     *
     * @param \OpenLoyalty\Domain\Pos\Pos $pos
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getByIdentifierAction(\OpenLoyalty\Domain\Pos\Pos $pos)
    {
        return $this->view($pos);
    }

    /**
     * Method will return complete list of POS.
     *
     * @Route(name="oloy.pos.list", path="/pos")
     * @Route(name="oloy.pos.seller.list", path="/seller/pos")
     * @Method("GET")
     * @Security("is_granted('LIST_POS')")
     *
     * @param Request $request
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getListAction(Request $request)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request);

        $posRepository = $this->get('oloy.pos.repository');
        $pos = $posRepository
            ->findAllPaginated(
                $pagination->getPage(),
                $pagination->getPerPage(),
                $pagination->getSort(),
                $pagination->getSortDirection()
            );
        $total = $posRepository->countTotal();

        return $this->view(
            [
                'pos' => $pos,
                'total' => $total,
            ],
            200
        );
    }
}
