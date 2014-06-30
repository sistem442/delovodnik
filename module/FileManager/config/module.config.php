<?php

namespace FileManager;

return array(
	'controllers' => array(
        'invokables' => array(
            'FileManager\Controller\FileManager' => 'FileManager\Controller\FileManagerController',		
        ),
    ),	
    'router' => array(
        'routes' => array(
			'file-manager' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/file-manager',
					'defaults' => array(
						'__NAMESPACE__' => 'FileManager\Controller',
						'controller'    => 'FileManager',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'default' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:controller[/:action[/:id]]]', // there is no constraints for id!
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'		=>	'[0-9]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
		),
	),
    'view_manager' => array(
        'template_path_stack' => array(
            'file-manager' => __DIR__ . '/../view'
        ),
		
		'display_exceptions' => true,
    ),
	// MODULE CONFIGURATIONS
	'module_config' => array(
		'upload_location'           => __DIR__ .  DIRECTORY_SEPARATOR.'..'.  DIRECTORY_SEPARATOR.'..'.  DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR.'public'.  DIRECTORY_SEPARATOR.'data'.  DIRECTORY_SEPARATOR.'uploads',
	),
	
);