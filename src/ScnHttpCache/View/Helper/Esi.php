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

    /**
     * This tells the view helper if the client understands surrogate capability
     *
     * @param  boolean $surrogateCapability
     * @return Esi
     */
    public function setSurrogateCapability($surrogateCapability)
    {
        $this->surrogateCapability = (bool) $surrogateCapability;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSurrogateCapability()
    {
        return $this->surrogateCapability;
    }

    /**
     * If the client does not understand surrogate capability, the config provider
     * is needed to fetch a configuration to boostrap another application request.
     *
     * @param  EsiApplicationConfigProviderInterface $esiApplicationConfigProvider
     * @return Esi
     */
    public function setEsiApplicationConfigProvider(EsiApplicationConfigProviderInterface $esiApplicationConfigProvider)
    {
        $this->esiApplicationConfigProvider = $esiApplicationConfigProvider;

        return $this;
    }

    /**
     * @throws ViewException\RuntimeException
     * @return EsiApplicationConfigProviderInterface
     */
    public function getEsiApplicationConfigProvider()
    {
        if (!$this->esiApplicationConfigProvider instanceof EsiApplicationConfigProviderInterface) {
            throw new ViewException\RuntimeException('Esi View Helper expects an instance of EsiApplicationConfigProviderInterface to be injected');
        }

        return $this->esiApplicationConfigProvider;
    }

    /**
     * This is largely here for testing purposes. In most cases,
     * The application should be initialized in getApplication
     *
     * @param  Application $application
     * @return Esi
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * When application is not available, one will be initialized to respond to
     * the esi request.
     *
     * @param  Uri                   $uri
     * @return \Zend\Mvc\Application
     */
    public function getApplication(Uri $uri)
    {
        if (!$this->application instanceof Application) {
            $applicationConfig = $this->getEsiApplicationConfigProvider()->getEsiApplicationConfig($uri);
            $this->application = Application::init($applicationConfig);
            // Remove the finish listeners so response header and content is not automatically echo'd
            $this->application->getEventManager()->clearListeners(MvcEvent::EVENT_FINISH);
        }

        // The request needs to be augmented with the URI passed in
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

    /**
     * If the client understands surrogate capability, return esi tag.
     * Otherwise, get a new application instance and run it, returning the content.
     *
     * @param  string $url
     * @return string
     */
    public function doEsi($url)
    {
        if ($this->getSurrogateCapability()) {
            return "<esi:include src=\"$url\" onerror=\"continue\" />\n";
        }

        // fallback to non-surrogate capability
        $uri = UriFactory::factory($url);
        $application = $this->getApplication($uri);
        $application->run();

        return $application->getResponse()->getContent();
    }
}
