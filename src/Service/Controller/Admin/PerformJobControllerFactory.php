<?php
namespace FixCliAndJob\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use FixCliAndJob\Controller\Admin\PerformJobController;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PerformJobControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new PerformJobController($serviceLocator);
    }
}
