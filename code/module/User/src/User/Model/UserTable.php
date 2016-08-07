<?php
    namespace User\Model;


    use Zend\Db\TableGateway\TableGateway;
    use Zend\Session\Container ;
    use Zend\Db\Sql\Select;
    use Zend\Db\ResultSet\ResultSet;
    class UserTable
    {
        protected $tableGateway;

        public function __construct(TableGateway $tableGateway)
        {
            $this->tableGateway = $tableGateway;
        }

        public function fetchAll()
        {
            $resultSet = $this->tableGateway->select();
            return $resultSet;
        }



        public function login($username, $password)
        {


            $rowset = $this->tableGateway->select(array('username'=> $username,'password '=> md5($password)));

            $row = $rowset->current();

            if (!$row) {

              return ;
            }

            $select = new \Zend\Db\Sql\Select ;
            $select->from('users');

            if($row->getTypes() =='D') {
                $select->join(array("d" => "doctor"), "d.doctorId = users.userId");

            }else {
               // $select->join(array("p" => "patient"), "p.userid = users.id");
                return ;

            }
            $select->where("users.userId = '{$row->getId()}'");


            $resultSet = $this->tableGateway->selectWith($select);
            $rowUser = $resultSet->current();

            $user_session = new Container('user');
            $user_session->offsetSet('login',true);
            $user_session->offsetSet('user',$rowUser);

            return $row;
        }

        public  function loginPatient($username, $password)
        {
            $select = new \Zend\Db\Sql\Select ;
            $select->from('users');

            $select->where(" username = '{$username}' and password = '".md5($password)."'");
            $select->join(array("p" => "patient"), "p.patientId = users.userId");

            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $rowUser = $resultSet->current();

            if($rowUser) {
                return $rowUser;
            }else {
                return array();
            }
        }

        public function saveUser(User $user)
        {

        }

        public function deleteUser($id)
        {
            $this->tableGateway->delete(array('id' => (int) $id));
        }
    }