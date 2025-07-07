<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailBundle\Mailer;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Class OloySymfonyMailer.
 */
class OloySymfonyMailer implements OloyMailer
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var TwigEngine
     */
    protected $twigEngine;

    /**
     * OloySymfonyMailer constructor.
     *
     * @param TwigEngine $twigEngine
     * @param MailerInterface $mailer
     */
    public function __construct(TwigEngine $twigEngine, MailerInterface $mailer)
    {
        $this->twigEngine = $twigEngine;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message)
    {
        $this->decorateMessage($message);

        $email = (new Email())
            ->subject($message->getSubject())
            ->from($message->getSenderEmail())
            ->to($message->getRecipientEmail())
            ->html($message->getContent())
            ->text($message->getPlainContent() ?: strip_tags($message->getContent()));

        if ($message->getSenderName()) {
            $email->from($message->getSenderEmail() . ' <' . $message->getSenderName() . '>');
        }

        if ($message->getRecipientName()) {
            $email->to($message->getRecipientEmail() . ' <' . $message->getRecipientName() . '>');
        }

        $this->mailer->send($email);

        return true;
    }

    /**
     * @param MessageInterface $message
     */
    protected function decorateMessage(MessageInterface $message)
    {
        // nothing to do
        if (!$this->twigEngine->exists($message->getTemplate())) {
            return;
        }

        $templateContent = $this->renderTemplateContent($message);

        $message->setContent($templateContent);
    }

    /**
     * @param MessageInterface $message
     *
     * @return string
     */
    protected function renderTemplateContent(MessageInterface $message): string
    {
        return $this->twigEngine->render($message->getTemplate(), $message->getParams());
    }
} 