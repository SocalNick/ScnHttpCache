<?php

namespace ScnHttpCache\View\Helper;

use ScnHttpCache\Service\EsiApplicationConfigProviderInterface;
use ScnHttpCache\View\Exception as ViewException;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Parameters;
use Zend\Uri\Uri;
use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;

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

    /**
     * @var Application
     */
    protected $application;

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
            throw new ViewException\RuntimeException('Esi View Helper expects an instance of EsiApplicationConfigProviderInterface to be injected');
        }

        return $this->esiApplicationConfigProvider;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    public function getApplication(Uri $uri)
    {
        if (!$this->application instanceof Application) {
            $applicationConfig = $this->getEsiApplicationConfigProvider()->getEsiApplicationConfig($uri);
            $this->application = Application::init($applicationConfig);
            $this->application->getEventManager()->clearListeners(MvcEvent::EVENT_FINISH);
        }

        $request = $this->application->getRequest();
        $request->setUri($uri);
        $request->setRequestUri($uri->getPath() . '?' . $uri->getQuery());
        $request->setQuery(new Parameters($uri->getQueryAsArray()));

        return $this->application;
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
            return $this->doEsi($url);
        }

        return $this;
    }

    public function doEsi($url)
    {
        if ($this->getSurrogateCapability()) {
            return "<esi:include src=\"$url\" onerror=\"continue\" />\n";
        }

        $uri = UriFactory::factory($url);

        $application = $this->getApplication($uri);

        $response = $application->run();

        return $response->getContent();
    }
}
