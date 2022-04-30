<?php

namespace Ayaz\AdminReadOnly\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

class ReadOnlyRole implements DataPatchInterface
{
    /**
     * RoleFactory
     *
     * @var roleFactory
     */
    private $roleFactory;

     /**
     * RulesFactory
     *
     * @var rulesFactory
     */
    private $rulesFactory;

    const ROLENAME = 'Role Admin Only';

    /**
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory
    ) {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /**
        * Create Role Admin Only role 
        */
        $role = $this->roleFactory->create();
        $role->setName(self::ROLENAME) //Role Name 
                ->setPid(0)
                ->setRoleType(RoleGroup::ROLE_TYPE) 
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();
        $resource = [
            'Magento_Backend::all'
        ];

        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}