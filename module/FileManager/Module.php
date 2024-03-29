<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace FileManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;

use FileManager\Model\Upload;
use FileManager\Model\UploadTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' .  __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getServiceConfig()
    {
    	return array(
    			'abstract_factories' => array(),
    			'aliases' => array(),
    			'factories' => array(
    				// SERVICES
    				'AuthService' => function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user','email','password', 'MD5(?)');
    							
    						$authService = new AuthenticationService();
    						$authService->setAdapter($dbTableAuthAdapter);
    						return $authService;
    				},
    				
    				// DB
    				'UserTable' =>  function($sm) {
    					$tableGateway = $sm->get('UserTableGateway');
    					$table = new UserTable($tableGateway);
    					return $table;
    				},
    				'UserTableGateway' => function ($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$resultSetPrototype = new ResultSet();
    					$resultSetPrototype->setArrayObjectPrototype(new User());
    					return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
    				},
    				
    				'UploadTable' =>  function($sm) {
    					$tableGateway = $sm->get('UploadTableGateway');
    					$uploadSharingTableGateway = $sm->get('UploadSharingTableGateway');
    					$table = new UploadTable($tableGateway, $uploadSharingTableGateway);
    					return $table;
    				},
    				'UploadTableGateway' => function ($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$resultSetPrototype = new ResultSet();
    					$resultSetPrototype->setArrayObjectPrototype(new Upload());
    					return new TableGateway('uploads', $dbAdapter, null, $resultSetPrototype);
    				},

    				'UploadSharingTableGateway' => function ($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					return new TableGateway('uploads_sharing', $dbAdapter);
    				},
    					    				
    				// FORMS
    				'LoginForm' => function ($sm) {
    					$form = new \FileManager\Form\LoginForm();
    					$form->setInputFilter($sm->get('LoginFilter'));
    					return $form;
    				},
    				'RegisterForm' => function ($sm) {
    					$form = new \FileManager\Form\RegisterForm();
    					$form->setInputFilter($sm->get('RegisterFilter'));
    					return $form;
    				},
    				'UserEditForm' => function ($sm) {
    					$form = new \FileManager\Form\UserEditForm();
    					$form->setInputFilter($sm->get('UserEditFilter'));
    					return $form;
    				},
    				'UploadForm' => function ($sm) {
    					$form = new \FileManager\Form\UploadForm();
    					return $form;
    				},
    				'UploadEditForm' => function ($sm) {
    					$form = new \FileManager\Form\UploadEditForm();
    					return $form;
    				},
    				'UploadShareForm' => function ($sm) {
    					$form = new \FileManager\Form\UploadShareForm();
    					return $form;
    				},
    				
    				// FILTERS
    				'LoginFilter' => function ($sm) {
    					return new \FileManager\Form\LoginFilter();
    				},
    				'RegisterFilter' => function ($sm) {
    					return new \FileManager\Form\RegisterFilter();
    					
    				},
    				'UserEditFilter' => function ($sm) {
    					return new \FileManager\Form\UserEditFilter();
    						
    				},
    				
    			),
    			'invokables' => array(),
    			'services' => array(),
    			'shared' => array(),
    	);
    }  
    
}
