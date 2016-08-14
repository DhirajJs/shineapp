<?php
 namespace User\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Zend\Session\Container ;
 use  User\Model\Auth;
 use Zend\View\Model\JsonModel;

 class UserController extends AbstractActionController
 {
     protected $_userTable;
     public function indexAction()
     {
         $this->layout('layout/login');
         return new ViewModel(array(
             'users' => $this->getUser()->fetchAll(),
         ));
     }

     public function loginAction()
     {

         $this->layout('layout/login');
         return array(
             'errorMessages'  => $this->flashmessenger()->getErrorMessages(),
             'successMessage'  => $this->flashmessenger()->getSuccessMessages()
         );
     }

    public  function accessAction()
    {
        $username = $this->getRequest()->getPost('username', null);
        $password  = $this->getRequest()->getPost('password', null);
        $auth = new Auth();
        $return = $auth->login($username,$password, $this->getUsersTable());

        if($return['status'] =='error') {
        $this->flashmessenger()->addErrorMessage($return['message']);

        return $this->redirect()
            ->toRoute('user',
                array(
                    'action' => 'login',

                )
            );
        }

        return $this->redirect()
            ->toRoute('dashboard',
                array(
                    'action' => 'index',
                )
            );

    }

     public  function createuserAction()
     {
         $data = $this->getRequest()->getPost();

         $return = $this->getUsersTable()->createDoctor($data);

         if($return['status'] =='error') {
             $this->flashmessenger()->addErrorMessage($return['message']);
             $this->layout('layout/login');
             return new ViewModel(array(
                 'data' => $data,
                 'errorMessages'  => $this->flashmessenger()->getErrorMessages(),
                 'successMessage'  => $this->flashmessenger()->getSuccessMessages()
             ));

         }
         //var_dump($return);die();
         $this->flashmessenger()->addSuccessMessage($return['message']);
         return $this->redirect()
             ->toRoute('user',
                 array(
                     'action' => 'login',

                     'successMessage'  => $this->flashmessenger()->getSuccessMessages()
                 )
             );

     }

     public  function accessajaxAction()
     {
         $username = $this->getRequest()->getPost('username', null);
         $password  = $this->getRequest()->getPost('password', null);
         //$auth = new Auth();
         $return = $this->getUsersTable()->loginPatient($username,$password);

         if(!$return) {
             $toReturn['status'] = 'error';
             $toReturn['message'] = 'Invalid username or password. Please try again';

         }else {
             $toReturn['status'] = 'success';
             $toReturn['message'] = 'You are now login';
             $toReturn['id'] = $return['id'];
             $toReturn['dbId'] = $return['patientId'];
         }

         return new JsonModel ( $toReturn);


     }

     public function LogoutAction()
     {

        $this->_logout();
        $this->flashmessenger()->addSuccessMessage('You are now log out');
         return $this->redirect()
             ->toRoute('user',
                 array('action' => 'login',
                 )
             );
     }


    protected function _logout()
    {
        $user_session = new Container('user');

        if ($user_session->offsetExists('login') && $user_session->offsetGet('login')) {
            $user_session->getManager()->destroy();
            $user_session->offsetUnset('login');
            $user_session->offsetUnset('user');
            $user_session->offsetUnset('username');
        }
    }

     public function isLoginAction()
     {

         $status['login'] = \User\Model\User::isLogin();

         return new JsonModel ( $status);

     }

     public function getUsersTable() {

         if (!$this->_userTable) {

             $sm = $this->getServiceLocator();

             $this->_userTable = $sm->get('User\Model\UserTable');
         }

         return $this->_userTable;
     }
 }