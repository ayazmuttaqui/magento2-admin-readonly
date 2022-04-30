<?php
/**
 * Copyright © Ayaz All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ayaz\AdminReadOnly\Observer\Backend\Model;

class SaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
        $this->_adminSession = $adminSession;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $allowed = [
            'Magento\Theme\Model\Theme',
            'Magento\Security\Model\AdminSessionInfo',
            'Magento\AdminNotification\Model\System\Message',
            'Magento\Ui\Model\Bookmark'
        ];
        $user = $this->_adminSession->getUser();
        if ($user) {
            $roleData = $user->getRole()->getRoleName();
            if ($roleData == \Ayaz\AdminReadOnly\Setup\Patch\Data\ReadOnlyRole::ROLENAME) {
                $model = $observer->getEvent()->getObject();
                $name = get_class($model);
                if (!in_array($name, $allowed)) {
                    $this->logger($name);
                    throw new \Exception("You are not allowed to make changes", 1);
                }
            }
        }
    }

    protected function logger($data) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/blocked.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}