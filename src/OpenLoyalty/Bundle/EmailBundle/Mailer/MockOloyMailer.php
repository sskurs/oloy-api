<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailBundle\Mailer;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;

/**
 * Mock mailer for testing purposes.
 */
class MockOloyMailer implements OloyMailer
{
    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message)
    {
        // Mock implementation - just return true without sending
        return true;
    }
} 