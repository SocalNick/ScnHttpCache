<?php

namespace ScnHttpCache\Service;

use Zend\Uri\UriInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class EsiApplicationConfigProviderAbstract implements
    EsiApplicationConfigProviderInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if (!$this->serviceLocator instanceof ServiceLocatorInterface) {
            // TODO Throw correct exception
            throw new \Exception('FIX EXCEPTION');
        }

        return $this->serviceLocator;
    }

    /**
     * Get EsiApplicationConfig based on UriInterface
     *
     * @param UriInterface
     * @return array
     */
    abstract public function getEsiApplicationConfig(UriInterface $uri);
}
