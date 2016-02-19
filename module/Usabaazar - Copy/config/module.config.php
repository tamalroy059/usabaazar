<?php

return array(
     'controllers' => array(
         'invokables' => array(
             'Usabaazar\Controller\Usabaazar' => 'Usabaazar\Controller\UsabaazarController',
         ),
     ),
	 
	 // The following section is new and should be added to your file
     'router' => array(
         'routes' => array(
             'usabaazar' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/usabaazar[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Usabaazar\Controller\Usabaazar',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
	 
     'view_manager' => array(
         'template_path_stack' => array(
             'usabaazar' => __DIR__ . '/../view',
         ),
     ),
 );