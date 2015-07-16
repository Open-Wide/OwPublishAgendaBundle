<?php

namespace OpenWide\Publish\AgendaBundle\LegacyMapper;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use eZ\Publish\Core\MVC\Legacy\Event\PreBuildKernelEvent;
use eZ\Publish\Core\MVC\Legacy\LegacyEvents;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class Configuration implements EventSubscriberInterface
{

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * Disables the feature when set using setEnabled()
     *
     * @var bool
     */
    private $enabled = true;

    public function __construct( ConfigResolverInterface $configResolver )
    {
        $this->configResolver = $configResolver;
    }

    /**
     * Toggles the feature
     *
     * @param bool $isEnabled
     */
    public function setEnabled( $isEnabled )
    {
        $this->enabled = (bool) $isEnabled;
    }

    public static function getSubscribedEvents()
    {
        return array(
            LegacyEvents::PRE_BUILD_LEGACY_KERNEL => array( "onBuildKernel", 128 )
        );
    }

    /**
     * Adds settings to the parameters that will be injected into the legacy kernel
     *
     * @param \eZ\Publish\Core\MVC\Legacy\Event\PreBuildKernelEvent $event
     */
    public function onBuildKernel( PreBuildKernelEvent $event )
    {
        if( !$this->enabled )
        {
            return;
        }
        
        $settings = array();
        $settings["owpagenda.ini/AgendaSettings/FolderNodeId"] = $this->configResolver->getParameter( "open_wide_publish_agenda.event_folder.location_id" );
        $event->getParameters()->set(
                "injected-settings", $settings + (array) $event->getParameters()->get( "injected-settings" )
        );
    }

}
