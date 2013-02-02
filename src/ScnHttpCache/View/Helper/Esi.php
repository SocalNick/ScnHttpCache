<?php

namespace ScnHttpCache\View\Helper;

use ScnHttpCache\Service\EsiApplicationConfigProviderInterface;
use Zend\Mvc\Application;
use Zend\Stdlib\Parameters;
use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;

class Esi extends AbstractHelper
{
    /**
     * @var EsiApplicationConfigProviderInterface
     */
    protected $esiApplicationConfigProvider;

    public function setEsiApplicationConfigProvider(EsiApplicationConfigProviderInterface $esiApplicationConfigProvider)
    {
        $this->esiApplicationConfigProvider = $esiApplicationConfigProvider;

        return $this;
    }

    public function getEsiApplicationConfigProvider()
    {
        if (!$this->esiApplicationConfigProvider instanceof EsiApplicationConfigProviderInterface) {
            // TODO - FIX EXCEPTION
            throw new \Exception('FIX EXCEPTION');
        }

        return $this->esiApplicationConfigProvider;
    }

    /**
     * By default provides the fluent interface,
     * but can also be invoked with a variable,
     * in which case, it will proxy to escapeHtml
     *
     * @return string|SafeEcho
     */
    public function __invoke($url = null)
    {
        if (null !== $url) {
            return $this->getTag($url);
        }

        return $this;
    }

    public function getTag($url)
    {
        $uri = UriFactory::factory($url);

        $applicationConfig = $this->getEsiApplicationConfigProvider()->getEsiApplicationConfig($uri);
        $application = Application::init($applicationConfig);

        $request = $application->getServiceManager()->get('Request');
        $request->setUri($uri);
        $request->setQuery(new Parameters($uri->getQueryAsArray()));

        $application->run();

//         return "<esi:include src=\"$url\" onerror=\"continue\" />\n";
    }
}
