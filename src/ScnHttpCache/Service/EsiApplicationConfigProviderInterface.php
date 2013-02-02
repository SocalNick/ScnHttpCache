<?php

namespace ScnHttpCache\Service;

use Zend\Uri\UriInterface;

interface EsiApplicationConfigProviderInterface
{
    /**
     * Get EsiApplicationConfig based on UriInterface
     *
     * @param UriInterface
     * @return array
     */
    public function getEsiApplicationConfig(UriInterface $uri);
}
