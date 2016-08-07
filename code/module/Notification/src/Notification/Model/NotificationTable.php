<?php
    namespace Notification\Model;


    use Zend\Db\TableGateway\TableGateway;
    use Zend\Session\Container ;
    use Zend\Db\Sql\Select;
    use Zend\Db\ResultSet\ResultSet;
    class NotificationTable
    {
        protected $tableGateway;

        public function __construct(TableGateway $tableGateway)
        {
            $this->tableGateway = $tableGateway;
        }

        public function fetchAll()
        {
            //SELECT patient.patientId,patient.name,patient.phone,doc_calendar.date FROM doc_calendar
           // LEFT JOIN patient ON patient.patientId = doc_calendar.patientId
            $select = new \Zend\Db\Sql\Select ;
            $select->from('doc_calendar');
            $user_session = new Container('user');

            $select->where(" doc_calendar.doctorId = '{$user_session['user']->getId()}'");
            $select->join(array("p" => "patient"), "doc_calendar.patientId = p.patientId");

            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();

            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $rows = array();
            foreach($resultSet as $row) {
                $rows[] = $row->getArrayCopy();
            }

            return $rows;
        }

        public function getPatient()
        {
            //SELECT patientId, name FROM patient WHERE doctorId = '".$docId."'"
            $select = new \Zend\Db\Sql\Select ;
            $select->from('patient');
            $user_session = new Container('user');

            $select->where(" doctorId = '{$user_session['user']->getId()}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();

            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $rows = array();
            foreach($resultSet as $row) {
                $rows[] = $row->getArrayCopy();
            }
            return $rows;
        }

        public function getNotification()
        {


            $select = new \Zend\Db\Sql\Select ;
            $user_session = new Container('user');
            $select->from('notifications');
            $select->join(array("p" => "patient"), "notifications.patientId = p.patientId");


            $select->where(" notifications.doctorId = '{$user_session['user']->getId()}'");
            $dbAdapter = $this->tableGateway->getAdapter();
            $statement = $dbAdapter->createStatement();

            $select->prepareStatement($dbAdapter, $statement);
            $driverResult = $statement->execute(); // execute statement to get result

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
            $rows = array();
            foreach($resultSet as $row) {
                $rows[] = $row->getArrayCopy();
            }
            return $rows;
        }


        public  function save($data)
        {
//INSERT INTO doc_calendar(patientId,doctorId,date)
           // VALUES('".$_POST['patientId']."','". $docId."','".$_POST['datetime']."')"

            $dbAdapter = $this->tableGateway->getAdapter();
            $user_session = new Container('user');
            $statement = $dbAdapter->createStatement(
                "INSERT INTO doc_calendar(patientId,doctorId,date) values ('{$data['patientId']}',
'{$user_session['user']->getId()}','{$data['datetime']}')"
            );

            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);

            $statement = $dbAdapter->createStatement(
                "INSERT INTO notifications(patientId,doctorId,date,request,approved) values ('{$data['patientId']}',
'{$user_session['user']->getId()}','{$data['datetime']}','{$data['request']}','Y')"
            );

            $driverResult = $statement->execute();
            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);
        }

        public function approve($data)
        {
            $dbAdapter = $this->tableGateway->getAdapter();



            $update = "UPDATE notifications SET approved='Y' WHERE notif_id=" . $_POST['notif_id'];
            $user_session = new Container('user');
            $statement = $dbAdapter->createStatement(
                "INSERT INTO doc_calendar(patientId,doctorId,date) values ('{$data['patientId']}',
'{$user_session['user']->getId()}','{$data['datetime']}')"
            );

            $driverResult = $statement->execute();

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);

            $statement = $dbAdapter->createStatement("UPDATE notifications SET approved='Y' 
WHERE notif_id='{$_POST['notif_id']}'");
            $driverResult = $statement->execute();

            $resultSet = new ResultSet();
            $resultSet->initialize($driverResult);

        }


    }