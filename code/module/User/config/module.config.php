<?php
 return array(
     'controllers' => array(
         'invokables' => array(
             'User\Controller\User' => 'User\Controller\UserController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'user' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/user[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                     ),
                     'defaults' => array(
                         'controller' => 'User\Controller\User',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
             'user' => __DIR__ . '/../view',

         ),
         'template_map' => array(
             'layout/login' => __DIR__ . '/../view/layout/login.phtml'
         ),

         'strategies' => array(
             'ViewJsonStrategy',
         ),

     ),
 );