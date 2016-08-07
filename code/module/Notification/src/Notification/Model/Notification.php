<?php
    namespace Notification\Model;
    use Zend\Db\Sql\Select;
    use Zend\Session\Container ;
    class Notification
    {
        protected $_notifId;
        protected $_date;
        protected $_request;
        protected $_approved;
        protected $_doctorId;
        protected $_patientId;

        /**
         * @return mixed
         */
        public function getNotifId()
        {
            return $this->_notifId;
        }

        /**
         * @param mixed $notifId
         */
        public function setNotifId($notifId)
        {
            $this->_notifId = $notifId;
        }

        /**
         * @return mixed
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * @param mixed $date
         */
        public function setDate($date)
        {
            $this->_date = $date;
        }

        /**
         * @return mixed
         */
        public function getRequest()
        {
            return $this->_request;
        }

        /**
         * @param mixed $request
         */
        public function setRequest($request)
        {
            $this->_request = $request;
        }

        /**
         * @return mixed
         */
        public function getApproved()
        {
            return $this->_approved?true:false;
        }

        /**
         * @param mixed $approved
         */
        public function setApproved($approved)
        {
            $this->_approved = $approved;
        }

        /**
         * @return mixed
         */
        public function getDoctorId()
        {
            return $this->_doctorId;
        }

        /**
         * @param mixed $doctorId
         */
        public function setDoctorId($doctorId)
        {
            $this->_doctorId = $doctorId;
        }

        /**
         * @return mixed
         */
        public function getPatientId()
        {
            return $this->_patientId;
        }

        /**
         * @param mixed $patientId
         */
        public function setPatientId($patientId)
        {
            $this->_patientId = $patientId;
        }



        public function getUsersTable() {

            if (!$this->_userTable) {

                $sm = $this->getServiceLocator();


                $this->_userTable = $sm->get('Notification\Model\NotificationTable');
            }

            return $this->_userTable;
        }


        public function exchangeArray($data)
        {
            $this->setNotifId((!empty($data['notif_id'])) ? $data['notif_id'] : null);
            $this->setDate((!empty($data['date'])) ? $data['date'] : null);
            $this->setApproved((!empty($data['approved'])) ? $data['approved'] : null);
            $this->setDoctorId((!empty($data['doctorId'])) ? $data['doctorId'] : null);
            $this->setPatientId((!empty($data['patientId'])) ? $data['patientId'] : null);
            $this->setRequest((!empty($data['request'])) ? $data['request'] : null);

        }


    }