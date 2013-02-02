<?php

namespace ScnHttpCache\Service;

use ScnHttpCache\View\Helper\Esi;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EsiViewHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $esiApplicationConfigProvider = $services->getServiceLocator()->get('ScnHttpCache-EsiApplicationConfigProviderInterface');

        $viewHelper = new Esi();
        $viewHelper->setEsiApplicationConfigProvider($esiApplicationConfigProvider);

        return $viewHelper;
    }
}
