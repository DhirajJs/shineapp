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
            $user_session = new Container('user');
            $docId = $user_session['user']->getId();
            $resultSet = $this->tableGateway->select();
            $select = new \Zend\Db\Sql\Select ;
            $select->from('patient');
            $select->where(" doctorId = '{$docId}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $patient = array();
            foreach ($resultSet as $row) {

                $storage = $row->getArrayCopy();
                $patient[] = $storage;
            }
            return $patient;
        }

        public  function  search($search)
        {
            $select = new \Zend\Db\Sql\Select ;
            $select->from('patient');
            $user_session = new Container('user');
            $docId = $user_session['user']->getId();
            $select->where(" doctorId = '{$docId}'");
            if($search) {

                $select->where(" doctorId = '{$docId}' and (name like '%{$search}%' or patientId = '{$search}')");
            }

            $resultSet = $this->tableGateway->selectWith($select);

            $rowUser = array();//$resultSet->current();
            foreach($resultSet as $result) {

                $rowUser[] = array('id'=>$result->getId(),'name'=>$result->getName());
            }
            return $rowUser;
        }

        public function loadView($id,$date='')
        {
            $patientDetails['details'] = $this->getPatient($id);
            $patientDetails['bloodPresure'] = $this->getBloodPressureInfo($id,$date);
            $patientDetails['diabetes'] = $this->getDiabetesInfo($id,$date);
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
        public function getDiabetesInfo($id,$date='')
        {
           // $sql = "SELECT * FROM diabetes WHERE patientId='{$_POST['id']}' order by `date` asc";
            if(!$date){
                $date = @date('Y-m', @strtotime('now'));
            }

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

        public function getBloodPressureInfo($id,$date='')
        {
            //  $sqlBP = "SELECT * FROM bloodpressure WHERE patientId='{$_POST['id']}' order by `date` asc";
            if(!$date) {
                $date = @date('Y-m', @strtotime('now'));
            }
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

        public function checkUser($username)
        {
            $select = new \Zend\Db\Sql\Select ;
            $select->from('users');
            $select->where(" username = '{$username}'");


            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();
            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $rowUser = $resultSet->current();

            if($rowUser) {
                return true;
            }else {
                return false;
            }

        }

        public  function save($data)
        {
            $user_session = new Container('user');

            $username = $data["username"];
            $password = $data["password"];
            $toReturn['message'] = '';
            $toReturn['status'] = 'error';
            if($this->checkUser($data['username'])) {
                $toReturn['message'] = 'Username already exist';
                $toReturn['status'] = 'error';
            }else {


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
                    "INSERT INTO users (username, password,types)
 VALUES ('{$username}','{$password}','P')"
                );

                $driverResult = $statement->execute();
                $resultSet = new ResultSet();
                $resultSet->initialize($driverResult);


                $statement = $dbAdapter->createStatement("INSERT INTO patient (`doctorId`,`name`,`email`,`phone`,`address`,`dob`,`patientId`,`gender`)
            VALUES ('{$docId}','{$name}','{$email}','{$phone}','{$address}','{$dob}','{$username}','{$gender}')"
                );

                $driverResult = $statement->execute();
                $resultSet = new ResultSet();

                $resultSet->initialize($driverResult);

                $nameR = $data["nameR"];
                $emailR = $data["emailR"];
                $phoneR = $data["telR"];

                $statement = $dbAdapter->createStatement("INSERT INTO responsiblePerson (`name`,`email`,`phone`,`patientId`)
            VALUES ('{$nameR}','{$emailR}','{$phoneR}','{$username}')"
                );

                $driverResult = $statement->execute();
                $resultSet = new ResultSet();

                $resultSet->initialize($driverResult);

                $toReturn['message'] = 'Your patient has been created';
                $toReturn['status'] = 'sucess';
            }
            return $toReturn;
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

        public function saveDetail($data)
        {
            $id = $data['id'];
            $read = explode(',',$data['data']);
            if(sizeof($read)>1) {

                $part1 = $this->rc4('health123',$this->hex2ascii($read[0])).','.$this->rc4('health123',$this->hex2ascii($read[1]));
                $detail = explode(',',$part1);
            }else {
                $detail = explode(',',$this->rc4('health123',$this->hex2ascii($data['data'])));
            }

            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');
            if(sizeof($detail)>7) {
                $statement = $dbAdapter->createStatement(
                    "INSERT INTO `bloodpressure` ( `date`, `systolic`, `diastolic`, `patientId`, `heartRate`)
VALUE('{$detail[2]}-{$detail[1]}-{$detail[0]} {$detail[3]}:{$detail[4]}','{$detail[5]}','{$detail[6]}','{$id}','{$detail[7]}')"
                );
            }else {
                $statement = $dbAdapter->createStatement(
                    "INSERT INTO `diabetes` ( `date`, `bloodSugarLevel`,  `patientId`,`fasting`)
VALUE('{$detail[2]}-{$detail[1]}-{$detail[0]} {$detail[3]}:{$detail[4]}','{$detail[5]}','{$id}', {$data['fasting']})"
                );
            }
//30 june 2016 at 14:22 systolic 103 diastolic 66 and pulse 97
//30,06,2016,14,22,103,66,97
            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);

        }

        public  function addmedication($data)
        {
            $id = $data['id'];
            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');

            $statement = $dbAdapter->createStatement(
                "INSERT INTO `consultation` ( `patientId`, `bmi`, `medication`, `date`, `doctorId`,`consultation_details`)
VALUE('{$id}','{$data['bmi']}','{$data['medication']}',now(),'{$user_session['user']->getId()}','{$data['consultation_details']}')"
            );
            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);

        }

        public function  updatemedication($data)
        {
            $id = $data['consultationId'];

            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');

            $statement = $dbAdapter->createStatement(
                "Update  `consultation`  set bmi ='{$data['bmi']}', 
                medication = '{$data['medication']}', date=now(),
                consultation_details ='{$data['consultation_details']}'
                where consultationId = $id
                ");
            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
        }

        public function rc4($key, $str) {
            $s = array();
            for ($i = 0; $i < 256; $i++) {
                $s[$i] = $i;
            }
            $j = 0;
            for ($i = 0; $i < 256; $i++) {
                $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
                $x = $s[$i];
                $s[$i] = $s[$j];
                $s[$j] = $x;
            }
            $i = 0;
            $j = 0;
            $res = '';
            for ($y = 0; $y < strlen($str); $y++) {
                $i = ($i + 1) % 256;
                $j = ($j + $s[$i]) % 256;
                $x = $s[$i];
                $s[$i] = $s[$j];
                $s[$j] = $x;
                $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
            }
            return str_replace('¬','',$res);
        }

       public function hex2ascii($str)
        {
            $p = '';
            for ($i=0; $i < strlen($str); $i=$i+2)
            {
                $p .= chr(hexdec(substr($str, $i, 2)));
            }
            return $p;
        }
    }