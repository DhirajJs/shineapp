<?php
 namespace Patient\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\Session\Container ;
 use Zend\View\Model\JsonModel;

 class PatientController extends AbstractActionController
 {
     protected $_userTable;
     public function indexAction()
     {
         return new ViewModel();
     }

     public function indexsearchAction()
     {
        // $this->layout('layout/patient');
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Search Patient ');
         return new ViewModel(array(
             'patients' => $this->getUsersTable()->fetchAll(),
         ));
     }

     public function viewAction()
     {
         //$this->layout('layout/patient');
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - View Patient Details');
         $id = $this->getRequest()->getPost('id', null);
         $date = $this->getRequest()->getPost('month', null);

         if(!$id) {
             $id = $this->getRequest()->getQuery('id', null);
         }

         $patientDetails =  $this->getUsersTable()->loadView($id,$date);

         return new ViewModel(array(
             'errorMessages'   => $this->flashmessenger()->getErrorMessages(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages(),
             'patientDetails'  => $patientDetails
         ));
     }

     public function updateAction()
     {
        // $this->layout('layout/patient');
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Update Patient');
         $data = $this->getRequest()->getPost();

         $patientDetails =  $this->getUsersTable()->update($data->toArray());
         $this->flashmessenger()->addSuccessMessage('Information was updated');
         return $this->redirect()
             ->toRoute('patient',
                 array(
                     'action' => 'view',

                 ),array( 'query' => array(
                     'id'=>$data['id'],
                 ))


             );;
     }

     public function addmedicationAction()
     {
         $data = $this->getRequest()->getPost();
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Add Medication');

         $this->getUsersTable()->addmedication($data->toArray());
         $this->flashmessenger()->addSuccessMessage('Medication added');
         return $this->redirect()
             ->toRoute('patient',
                 array(
                     'action' => 'view',

                 ),array( 'query' => array(
                     'id'=>$data['id'],
                 ))


             );
     }

     public function updatemedicationAction()
     {
         $data = $this->getRequest()->getPost();

         $this->getUsersTable()->updatemedication($data->toArray());
         $this->flashmessenger()->addSuccessMessage('Medication updated');
         return $this->redirect()
             ->toRoute('patient',
                 array(
                     'action' => 'view',

                 ),array( 'query' => array(
                     'id'=>$data['id'],
                 ))


             );
     }

     public function updateajaxAction()
     {
        // $this->layout('layout/patient');
         $data = $this->getRequest()->getPost();

         $patientDetails =  $this->getUsersTable()->update($data->toArray());

         return new JsonModel(array(
             'message'  => 'Your details was updated',

         ));
     }

     public function viewajaxAction()
     {
         //$this->layout('layout/patient');
         $id = $this->getRequest()->getPost('id', null);
         $date = $this->getRequest()->getPost('date', null);

         $patientDetails =  $this->getUsersTable()->loadView($id,$date);

         return new JsonModel(array(
             'errorMessages'   => $this->flashmessenger()->getErrorMessages(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages(),
             'patientDetails'  => $patientDetails
         ));
     }

     public function responsibledetailAction()
     {
       //  $this->layout('layout/patient');
         $id = $this->getRequest()->getPost('id', null);
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - View Responsible Party details');
         $responsible =  $this->getUsersTable()->getResponsible($id);

         return new JsonModel(array(

             'responsible'  => $responsible
         ));
     }

     public function updateresAction()
     {
         //$this->layout('layout/patient');
         $data = $this->getRequest()->getPost();

          $this->getUsersTable()->updateRes($data->toArray());

         return new JsonModel(array(
             'message'  => 'Your details was updated',

         ));
     }

     public function ajaxsearchAction()
     {
         $search = $this->getRequest()->getPost('search', null);

        // $patient = ;

         return new JsonModel (array(
             'patients' => $this->getUsersTable()->search($search),
         ));
     }

     public function getUsersTable() {

         if (!$this->_userTable) {

             $sm = $this->getServiceLocator();

             $this->_userTable = $sm->get('Patient\Model\PatientTable');
         }

         return $this->_userTable;
     }

     public function  addAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Add Patient');
         return new ViewModel(array(

             'successMessage'  => $this->flashmessenger()->getSuccessMessages()
         ));
     }


     public function settingAction()
     {
         $this->getServiceLocator()->get('ViewHelperManager')->get('HeadTitle')->set('Doctor App - Setting');
         return new ViewModel(array(

             'doctor'  => $this->getUsersTable()->getDoctor(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()
         ));
     }

     public function  saveAction()
     {
        // $this->flashmessenger()->addSuccessMessage('Patient Saved');
         $data = $this->getRequest()->getPost();
         $return = $this->getUsersTable()->save($data);
         if($return['status'] =='error') {
             $this->flashmessenger()->addErrorMessage($return['message']);

             return new ViewModel(array(
                 'data' => $data,
                 'errorMessages'  => $this->flashmessenger()->getErrorMessages(),
                 'successMessage'  => $this->flashmessenger()->getSuccessMessages()
             ));

         }
         $this->flashmessenger()->addSuccessMessage($return['message']);
         return $this->redirect()
             ->toRoute('patient',
                 array(
                     'action' => 'add',
                     'successMessage'  => $this->flashmessenger()->getSuccessMessages()
                 )
             );
     }

     public function doctorsaveAction()
     {
         $data = $this->getRequest()->getPost();
         $this->getUsersTable()->saveDoctor($data);
         $this->flashmessenger()->addSuccessMessage('Information was updated');

         return $this->redirect()
             ->toRoute('patient',
                 array(
                     'action' => 'setting',
                 )
             );
     }

     public function savedetailAction()
     {
         $data = $this->getRequest()->getPost();
         
         $this->getUsersTable()->saveDetail($data);
         return new JsonModel (array(
             'message' => 'success',
         ));
     }
 }