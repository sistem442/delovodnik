<?php
return array(
	'controllers' => array(
        'invokables' => array(
            'Delovodnik\Controller\Delovodnik' => 'Delovodnik\Controller\DelovodnikController',

		),
	),
		
	'router' => array(
        'routes' => array(
			'search' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/search',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Delovodnik\Controller',
								'controller'    => 'Delovodnik',
								'action'        => 'search',
                    ),
                ),
            ),
			'delovodnik' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/delovodnik',
					'defaults' => array(
						'__NAMESPACE__' => 'Delovodnik\Controller',
						'controller'    => 'Delovodnik',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'default' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:controller[/:action[/:id]]]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     	 => '[0-9]*',
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
//        'template_map' => array(
//            'layout/Auth'           => __DIR__ . '/../view/layout/Auth.phtml',
//        ),
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),

        'template_path_stack' => array(
            'delovodnik' => __DIR__ . '/../view'
        ),
    ),
	
);