<?php

namespace ScnHttpCache\View\Helper;

use ScnHttpCache\Service\EsiApplicationConfigProviderInterface;
use Zend\Mvc\Application;
use Zend\Stdlib\Parameters;
use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\MvcEvent;

class Esi extends AbstractHelper
{
    /**
     * @var boolean
     */
    protected $surrogateCapability = false;

    /**
     * @var EsiApplicationConfigProviderInterface
     */
    protected $esiApplicationConfigProvider;

    public function setSurrogateCapability($surrogateCapability)
    {
        $this->surrogateCapability = (bool) $surrogateCapability;

        return $this;
    }

    public function getSurrogateCapability()
    {
        return $this->surrogateCapability;
    }

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
        if ($this->getSurrogateCapability()) {
            return "<esi:include src=\"$url\" onerror=\"continue\" />\n";
        }

        $uri = UriFactory::factory($url);

        $applicationConfig = $this->getEsiApplicationConfigProvider()->getEsiApplicationConfig($uri);
        $application = Application::init($applicationConfig);
        $application->getEventManager()->clearListeners(MvcEvent::EVENT_FINISH);

        $request = $application->getServiceManager()->get('Request');
        $request->setUri($uri);
        $request->setRequestUri($uri->getPath() . '?' . $uri->getQuery());
        $request->setQuery(new Parameters($uri->getQueryAsArray()));

        $response = $application->run();

        return $response->getContent();
    }
}
