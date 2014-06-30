<?php

namespace Delovodnik;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;

use Delovodnik\Model\Data;
use Delovodnik\Model\DataTable;
use Delovodnik\Model\Files;
use Delovodnik\Model\FilesTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
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
    				'DataTable' =>  function($sm) {
    					$tableGateway = $sm->get('DataTableGateway');
    					$table = new DataTable($tableGateway);
    					return $table;
    				},
    				'DataTableGateway' => function ($sm) {
    					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					$resultSetPrototype = new ResultSet();
    					$resultSetPrototype->setArrayObjectPrototype(new Data());
    					return new TableGateway('data', $dbAdapter, null, $resultSetPrototype);
    				},
    				
    				'FileTable' =>  function($sm) {
    					$tableGateway = $sm->get('FileTableGateway');
    					$fileTableGateway = $sm->get('FileTableGateway');
    					$table = new UploadTable($tableGateway, $fileTableGateway);
    					return $table;
    				},
    					
    				// FORMS
    				'LoginForm' => function ($sm) {
    					$form = new \Users\Form\LoginForm();
    					$form->setInputFilter($sm->get('LoginFilter'));
    					return $form;
    				},
    				'RegisterForm' => function ($sm) {
    					$form = new \Users\Form\RegisterForm();
    					$form->setInputFilter($sm->get('RegisterFilter'));
    					return $form;
    				},
    				'UserEditForm' => function ($sm) {
    					$form = new \Users\Form\UserEditForm();
    					$form->setInputFilter($sm->get('UserEditFilter'));
    					return $form;
    				},
    				'UploadForm' => function ($sm) {
    					$form = new \Users\Form\UploadForm();
    					return $form;
    				},
    				'UploadEditForm' => function ($sm) {
    					$form = new \Users\Form\UploadEditForm();
    					return $form;
    				},
					'SearchDateForm' => function ($sm) {
    					$form = new \Delovodnik\Form\SearchDateForm();
    					return $form;
    				},
					'SearchForm' => function ($sm) {
    					$form = new \Delovodnik\Form\SearchForm();
    					return $form;
    				},
    				
    				// FILTERS
    				'LoginFilter' => function ($sm) {
    					return new \Users\Form\LoginFilter();
    				},
    				'RegisterFilter' => function ($sm) {
    					return new \Users\Form\RegisterFilter();
    					
    				},
    				'UserEditFilter' => function ($sm) {
    					return new \Users\Form\UserEditFilter();
    						
    				},
    				
    			),
    			'invokables' => array(),
    			'services' => array(),
    			'shared' => array(),
    	);
    }
}

