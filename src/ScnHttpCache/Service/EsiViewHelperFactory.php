<?php

namespace ScnHttpCache\Service;

use ScnHttpCache\View\Helper\Esi;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EsiViewHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $viewHelperPluginManager)
    {
        $serviceLocator = $viewHelperPluginManager->getServiceLocator();

        $esiApplicationConfigProvider = $serviceLocator->get('ScnHttpCache-EsiApplicationConfigProviderInterface');

        $viewHelper = new Esi();
        $viewHelper->setEsiApplicationConfigProvider($esiApplicationConfigProvider);
        $viewHelper->setResponse($serviceLocator->get('Response'));

        $request = $serviceLocator->get('Request');
        $headers = $request->getHeaders();
        if (
            $headers->has('surrogate-capability')
            && false !== strpos($headers->get('surrogate-capability')->getFieldValue(), 'ESI/1.0')
        ) {
            $viewHelper->setSurrogateCapability(true);
        }

        return $viewHelper;
    }
}
