<?php
 namespace Dashboard\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\Session\Container ;

 class DashboardController extends AbstractActionController
 {

     public function indexAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Dashboard');
        // $this->layout('layout/dashboard');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         return new ViewModel();
     }

     public function consultationAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - View Consulation');
        // $this->layout('layout/dashboard');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         return new ViewModel(
            array( 'countPatient'=>'')
         );
     }


 }