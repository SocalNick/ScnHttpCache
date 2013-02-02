<?php
return array(
    'service_manager' => array(
        'aliases' => array(
            'EsiApplicationConfig' => 'ApplicationConfig',
        ),
        'invokables' => array(
            'ScnHttpCache-EsiApplicationConfigProviderInterface' => 'ScnHttpCache\Service\EsiApplicationConfigProvider',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'esi' => 'ScnHttpCache\Service\EsiViewHelperFactory',
        ),
    ),
);
