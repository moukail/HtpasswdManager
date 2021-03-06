<?php

/**
 * Steven Bühner
 * 
 * @copyright Steven Bühner
 * @license MIT
 */
namespace HtpasswdManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use HtpasswdManager\Service\HtpasswdService;
use HtpasswdManager\Service\UserService;

class Module implements AutoloaderProviderInterface {

	public function getAutoloaderConfig() {
		return array( 
				'Zend\Loader\StandardAutoloader' => array( 
						'namespaces' => array( 
								// if we're in a namespace deeper than one level we need to fix the \ in the path
								__NAMESPACE__ => __DIR__ . '/src/' . str_replace ( '\\', '/', __NAMESPACE__ ) 
						) 
				) 
		);
	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap(MvcEvent $e) {
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
	}

	public function getServiceConfig() {
		return array( 
				'factories' => array( 
						'HtpasswdManager\Service\HtpasswdService' => function ($sm) {
							$config = $sm->get ( 'Config' );
							
							if (! isset ( $config ['HtpasswdManager'] ) || ! is_array ( $config ['HtpasswdManager'] ) || ! isset ( $config ['HtpasswdManager'] ['htpasswd'] ) || empty ( $config ['HtpasswdManager'] ['htpasswd'] )) {
								throw new \Exception ( 'HtpasswdManager Config not found' );
							}
							
							$htpasswd_filename = $config ['HtpasswdManager'] ['htpasswd'];
							$service = new HtpasswdService ( $htpasswd_filename );
							
							return $service;
						} 
				) 
		);
	}

}