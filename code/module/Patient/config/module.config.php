<?php
 return array(
     'controllers' => array(
         'invokables' => array(
             'Patient\Controller\Patient' => 'Patient\Controller\PatientController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'patient' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/patient[/:action][/:param][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'param' => '[a-zA-Z][a-zA-Z0-9_-]+',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Patient\Controller\Patient',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
             'patient' => __DIR__ . '/../view',

         ),
         'template_map' => array(
             'layout/patient' => __DIR__ . '/../view/layout/patient.phtml'
         ),

         'strategies' => array(
             'ViewJsonStrategy',
         ),

     ),
 );