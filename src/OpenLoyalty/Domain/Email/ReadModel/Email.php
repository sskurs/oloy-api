<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email\ReadModel;

use Broadway\ReadModel\SerializableReadModel;
use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class Email.
 */
class Email implements SerializableReadModel
{
    /**
     * @var EmailId
     */
    protected $emailId;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Email constructor.
     *
     * @param EmailId   $emailId
     * @param string    $key
     * @param string    $subject
     * @param string    $content
     * @param string    $senderName
     * @param string    $senderEmail
     * @param \DateTime $updatedAt
     */
    public function __construct(
        EmailId $emailId,
        string $key,
        string $subject,
        string $content,
        string $senderName,
        string $senderEmail,
        \DateTime $updatedAt
    ) {
        $this->emailId = $emailId;
        $this->key = $key;
        $this->subject = $subject;
        $this->content = $content;
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->getEmailId();
    }

    /**
     * @return string
     */
    public function getEmailId(): string
    {
        return $this->emailId->__toString();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $updatedAt = new \DateTime();
        $updatedAt->setTimestamp($data['updatedAt']);

        return new self(
            new EmailId($data['emailId']),
            $data['key'],
            $data['subject'],
            $data['content'],
            $data['senderName'],
            $data['senderEmail'],
            $updatedAt
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'emailId' => $this->emailId->__toString(),
            'key' => $this->key,
            'subject' => $this->subject,
            'content' => $this->content,
            'senderName' => $this->senderName,
            'senderEmail' => $this->senderEmail,
            'updatedAt' => $this->updatedAt->getTimestamp(),
        ];
    }
}
