<?php
 namespace Notification\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\Session\Container ;
 use Zend\View\Model\JsonModel;

 class NotificationController extends AbstractActionController
 {
     protected $_userTable;
     public function IndexAction()
     {

     }

     public function  calenderAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - View Calender');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         return new ViewModel(array(
             'calender' => $this->getUsersTable()->fetchAll(),

         ));
     }

     public function  viewAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - View Notification');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         return new ViewModel(array(
             'notification' => $this->getUsersTable()->getNotification(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()

         ));
     }
     public function  approveAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Approve Notification');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         $this->flashmessenger()->addSuccessMessage('Appointment approved');
         $data = $this->getRequest()->getPost();
         $this->getUsersTable()->approve($data);
         return $this->redirect()
             ->toRoute('notification',
                 array(
                     'action' => 'view',
                 )
             );
     }

     public function  addAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Add Notification');
         if(!\User\Model\User::isLogin()) {
             return $this->redirect()
                 ->toRoute('user',
                     array('action' => 'login',
                     )
                 );
         }
         return new ViewModel(array(
             'patient' => $this->getUsersTable()->getPatient(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()
         ));
     }



     public function  saveAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Save Notification');
         $this->flashmessenger()->addSuccessMessage('Appointment Saved');
         $data = $this->getRequest()->getPost();
         $data['approved'] = 'Y';
         $this->getUsersTable()->save($data);
         return $this->redirect()
             ->toRoute('notification',
                 array(
                     'action' => 'add',
                 )
             );
     }

     public function  saveajaxAction()
     {
         $this->flashmessenger()->addSuccessMessage('Appointment Saved');
         $data = $this->getRequest()->getPost();
         $this->getUsersTable()->loadDoctorByPatientId($data['patientId']);
         $data['approved'] = 'N';
         $this->getUsersTable()->save($data);
         return new JsonModel(array(
             'message'  => 'Your appointment is being reviewed',

         ));

     }

     public function deleteAction()
     {
         $id = $this->getRequest()->getQuery('id', null);

         if($id) {
             $this->getUsersTable()->deleteNotification($id);
         }
         return $this->redirect()
             ->toRoute('notification',
                 array(
                     'action' => 'view',
                 )
             );
     }

     public function getUsersTable() {

         if (!$this->_userTable) {

             $sm = $this->getServiceLocator();

             $this->_userTable = $sm->get('Notification\Model\NotificationTable');
         }

         return $this->_userTable;
     }

 }