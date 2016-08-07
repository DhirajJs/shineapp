<?php
    namespace Patient\Model;


    use Zend\Db\TableGateway\TableGateway;
    use Zend\Session\Container ;
    use Zend\Db\Sql\Select;
    use Zend\Db\ResultSet\ResultSet;

    class PatientTable
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

        public  function  search($search)
        {
            $select = new \Zend\Db\Sql\Select ;
            $select->from('patient');
            if($search) {
                $select->where(" name like '%{$search}%' or patientId = '{$search}'");
            }

            $resultSet = $this->tableGateway->selectWith($select);

            $rowUser = array();//$resultSet->current();
            foreach($resultSet as $result) {

                $rowUser[] = array('id'=>$result->getId(),'name'=>$result->getName());
            }
            return $rowUser;
        }

        public function loadView($id)
        {
            $patientDetails['details'] = $this->getPatient($id);
            $patientDetails['bloodPresure'] = $this->getBloodPressureInfo($id);
            $patientDetails['diabetes'] = $this->getDiabetesInfo($id);
            $patientDetails['medication'] = $this->getMedication($id);

            return $patientDetails;
        }

        public  function getMedication($id)
        {
            //$sqlC = "SELECT * FROM consultation WHERE patientId='" . $_POST['id'] . "'" . "AND doctorId='" . $docId . "'";

            $select = new Select();
            $select->from('consultation')->where("  patientId = '{$id}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $consultation = array();
            foreach ($resultSet as $row) {
                $storage = $row->getArrayCopy();
                $consultation[] = $storage;
            }

            return $consultation;

        }
        public function getDiabetesInfo($id)
        {
           // $sql = "SELECT * FROM diabetes WHERE patientId='{$_POST['id']}' order by `date` asc";
            $date = @date('Y-m',@strtotime('now'));
            $select = new Select();
            $select->from('diabetes')->where("  patientId = '{$id}' and `date` like '{$date}%' order by `date` asc");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $diabetes = array();
            foreach ($resultSet as $row) {
                $storage = $row->getArrayCopy();
                $diabetes[] = $storage;
            }

            return $diabetes;

        }

        public function getBloodPressureInfo($id)
        {
            //  $sqlBP = "SELECT * FROM bloodpressure WHERE patientId='{$_POST['id']}' order by `date` asc";
            $date = @date('Y-m',@strtotime('now'));
            $select = new Select();
            $select->from('bloodpressure')->where("  patientId = '{$id}' and `date` like '{$date}%' order by `date` asc");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $bloodPressure = array();
            foreach ($resultSet as $row) {
                $storage = $row->getArrayCopy();
                $bloodPressure[] = $storage;
            }

            return $bloodPressure;

        }

        public function getResponsible($id)
        {

            $select = new Select();
            $select->from('responsiblePerson')->where("  patientId = '{$id}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $responsible = array();
            $responsible = $resultSet->current()->getArrayCopy();


            return $responsible;
        }

        public function getPatient($id)
        {
            $select = new \Zend\Db\Sql\Select ;
            $select->from('patient')->where("  patientId = '{$id}'");

            $resultSet = $this->tableGateway->selectWith($select);
            if(!$resultSet) {
                $this->flashmessenger()->addErrorMessage('Invalid Patient');
            }else {
                $rowUser = array();
                foreach($resultSet as $result) {
                    $age = @strtotime('now') - (@strtotime($result->getDob()));

                $rowUser = array(
                        'id'=>$result->getId(),
                        'name'=>$result->getName(),
                        'email'=>$result->getEmail(),
                        'doctorId'=>$result->getDoctorId(),
                        'dob'=>$result->getDob(),
                        'address'=>$result->getAddress(),
                        'phone'=>$result->getPhone(),
                        'gender'=>$result->getGender(),
                        'age'=>floor($age/31556926),
                    );
                }
                return $rowUser;
            }
            return false;
        }

        public function deletePatient($id)
        {
            $this->tableGateway->delete(array('id' => (int) $id));
        }

        public function update($data)
        {

            $rowUser = array(
                'name'=>$data['name'],
                'email'=>$data['email'],
                'dob'=>$data['dob'],
                'address'=>$data['address'],
                'phone'=>$data['phone'],
                'gender'=>$data['gender']
            );
            $this->tableGateway->update($rowUser, array(
                 'patientId = ?'        => $data['id']
             ));

        }


        public function updateRes($data)
        {
            $rowUser = array(
                'name'=>$data['name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],

            );
            //$update = new \Zend\Db\Sql\Update();

            $dbAdapter = $this->tableGateway->getAdapter();

            $statement = $dbAdapter->createStatement(
                "UPDATE responsiblePerson SET name = '{$data['name']}',email='{$data['email']}',phone='{$data['phone']}'
        WHERE responsiblePersonId = '{$data['responsiblePersonId']}'"
            );
           // $update->prepareStatement($dbAdapter, $statement);

            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
        }

        public  function save($data)
        {
            $user_session = new Container('user');

            $username = $data["username"];
            $password = $data["password"];
            $userId = $data["userId"];

            $name = $data["name"];
            $email = $data["email"];
            $phone = $data["phone"];
            $address = $data["address"];
            $dob = $data["dob"];
            $gender = $data["gender"];
            $docId = $user_session['user']->getId();

            $password = md5($password);
// $sql = "INSERT INTO users (username, password,userId,types) VALUES ('".$username."','". $password."','".$userId."','".$types."')";
//            $sql1= "INSERT INTO patient (doctorId,name,email,phone,address,dob,patientId) VALUES ('".$docId."','". $name."','".$email."','".$phone."','".$address."','".$dob."','".$userId."')";

            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');
            $statement = $dbAdapter->createStatement(
                "INSERT INTO users (username, password,userId,types)
 VALUES ('{$username}','{$password}','{$userId}','P')"
            );

            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);


            $statement = $dbAdapter->createStatement("INSERT INTO patient (`doctorId`,`name`,`email`,`phone`,`address`,`dob`,`patientId`,`gender`)
            VALUES ('{$docId}','{$name}','{$email}','{$phone}','{$address}','{$dob}','{$userId}','{$gender}')"
            );

            $driverResult = $statement->execute();
            $resultSet = new ResultSet();

            $resultSet->initialize($driverResult);
            ;
        }

        public function getDoctor()
        {
            $user_session = new Container('user');
            $select = new Select();
            $select->from('doctor')->where("  doctorId = '{$user_session['user']->getId()}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $responsible = array();
            $doctor = $resultSet->current()->getArrayCopy();


            return $doctor;
        }

        public function saveDoctor($data)
        {
            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');

            $statement = $dbAdapter->createStatement(
                "UPDATE doctor SET name = '{$data['name']}',email='{$data['email']}',phone='{$data['phone']}'
            , address ='{$data['address']}'
        WHERE doctorId = '{$user_session['user']->getId()}'"
            );
            // $update->prepareStatement($dbAdapter, $statement);

            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
        }
    }