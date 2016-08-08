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
         return new ViewModel(array(
             'calender' => $this->getUsersTable()->fetchAll(),

         ));
     }

     public function  viewAction()
     {
         return new ViewModel(array(
             'notification' => $this->getUsersTable()->getNotification(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()

         ));
     }
     public function  approveAction()
     {
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
         return new ViewModel(array(
             'patient' => $this->getUsersTable()->getPatient(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()
         ));
     }



     public function  saveAction()
     {
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
             'message'  => 'Your appointmnent is being reviewed',

         ));

     }

     public function getUsersTable() {

         if (!$this->_userTable) {

             $sm = $this->getServiceLocator();

             $this->_userTable = $sm->get('Notification\Model\NotificationTable');
         }

         return $this->_userTable;
     }

 }