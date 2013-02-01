<?php

namespace ScnHttpCache\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Esi extends AbstractHelper
{
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
        return "<esi:include src=\"$url\" onerror=\"continue\" />\n";
    }
}
