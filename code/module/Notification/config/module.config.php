<?php
 return array(
     'controllers' => array(
         'invokables' => array(
             'Notification\Controller\Notification' => 'Notification\Controller\NotificationController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'notification' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/notification[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Notification\Controller\Notification',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
             'Notification' => __DIR__ . '/../view',

         ),
         'template_map' => array(
             'layout/login' => __DIR__ . '/../view/layout/login.phtml'
         ),

         'strategies' => array(
             'ViewJsonStrategy',
         ),

     ),
 );