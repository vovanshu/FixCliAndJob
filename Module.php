<?php

namespace FixCliAndJob;

if (!class_exists(\Common\TraitModule::class)) {
    require_once dirname(__DIR__) . '/Common/TraitModule.php';
}

if (!class_exists(\FixCliAndJob\Common::class)) {
    require_once __DIR__ . '/Common.php';
}

use Laminas\Mvc\MvcEvent;
use Omeka\Module\AbstractModule;
use Common\TraitModule;
use FixCliAndJob\Common;

class Module extends AbstractModule
{

    use TraitModule;
    use Common;

    const NAMESPACE = __NAMESPACE__;

    public function onBootstrap(MvcEvent $event): void
    {

        parent::onBootstrap($event);
        $this->addDefAclRules();

    }


    /**
     * Add ACL rules for this module.
     */
     protected function addDefAclRules()
     {

        $this->getAcl()
            ->allow(
                null,
                [
                    Controller\Admin\PerformJobController::class,
                ],
                [
                    'execute', 'executing'
                ]
            );

        $this->getAcl()
            ->allow(
                [
                    \Omeka\Permissions\Acl::ROLE_GLOBAL_ADMIN,
                    \Omeka\Permissions\Acl::ROLE_SITE_ADMIN
                ],
                [
                    Controller\Admin\PerformJobController::class,
                ],
                [
                    'testingloop'
                ]
            );

    }

}
