<?php
    namespace User\Model;
    use Zend\Db\Adapter\Adapter;
    use Zend\Db\ResultSet\ResultSet;
    use Zend\Db\TableGateway\AbstractTableGateway;
    use Zend\Session\Container ;

    class Auth extends AbstractTableGateway
    {

        public function login($username, $password, $table)
        {
            $toReturn = array('message'=>'','status'=> 'success','id'=>null,'dbId'=>'');
            if(empty($username) || empty($password) ) {
                $toReturn['status'] = 'error';
                $toReturn['message'] = 'Invalid username or password. Please try again';
            }

            $resultSet = $table->login($username, $password);
            if(!$resultSet) {
                $toReturn['status'] = 'error';
                $toReturn['message'] = 'Invalid username or password. Please try again';

            }else {
                $toReturn['status'] = 'success';
                $toReturn['message'] = 'You are now login';
                $toReturn['id'] = $resultSet->getId();
                $toReturn['dbId'] = $resultSet->getDbId();
            }

            return $toReturn;
        }

        public function logout()
        {

        }

    }