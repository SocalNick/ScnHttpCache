<?php

namespace ScnHttpCache\Service;

use Zend\Uri\UriInterface;

class EsiApplicationConfigProvider extends EsiApplicationConfigProviderAbstract
{

    /**
     * Get EsiApplicationConfig based on UriInterface
     * The default implementation ignores the URI
     *
     * @param UriInterface
     * @return array
     */
    public function getEsiApplicationConfig(UriInterface $uri)
    {
        return $this->getServiceLocator()->get('EsiApplicationConfig');
    }
}
