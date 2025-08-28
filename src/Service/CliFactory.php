<?php
namespace FixCliAndJob\Service;

use FixCliAndJob\Stdlib\Cli;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
// use Omeka\Service\Exception\RuntimeException;

class CliFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new Cli($serviceLocator, Null);
    }
}
