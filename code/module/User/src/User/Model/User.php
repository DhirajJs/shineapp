<?php
    namespace User\Model;
    use Zend\Db\Sql\Select;
    use Zend\Session\Container ;
    class User
    {
        protected $_id;
        protected $_name;
        protected $_email;
        protected $_doctorId;
        protected $_dob;
        protected $_address;
        protected $_phone;
        protected $_gender;
        protected $_username;
        protected $_types;
        protected $_userTable;
        protected $_dbId;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->_id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id)
        {
            $this->_id = $id;
        }

        /**
         * @return mixed
         */
        public function getDbId()
        {
            return $this->_dbId;
        }

        /**
         * @param mixed $dbId
         */
        public function setDbId($dbId)
        {
            $this->_dbId = $dbId;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * @return mixed
         */
        public function getEmail()
        {
            return $this->_email;
        }

        /**
         * @param mixed $email
         */
        public function setEmail($email)
        {
            $this->_email = $email;
        }

        /**
         * @return mixed
         */
        public function getDoctorId()
        {

            if ($this->getTypes() != 'D') {
                return $this->_doctorId;
            }
        }

        /**
         * @param mixed $doctorId
         */
        public function setDoctorId($doctorId)
        {
            if ($this->getTypes() != 'D') {
                $this->_doctorId = $doctorId;
            }
        }

        /**
         * @return mixed
         */
        public function getDob()
        {
            if ($this->getTypes() != 'P') {
                return $this->_dob;
            } else {
                throw new MyException('Invalid Method', 5);
            }
        }

        /**
         * @param mixed $dob
         */
        public function setDob($dob)
        {
            if ($this->getTypes() != 'P') {
                $this->_dob = $dob;
            }
        }

        /**
         * @return mixed
         */
        public function getAddress()
        {
            return $this->_address;
        }

        /**
         * @param mixed $address
         */
        public function setAddress($address)
        {
            $this->_address = $address;
        }

        /**
         * @return mixed
         */
        public function getPhone()
        {
            return $this->_phone;
        }

        /**
         * @param mixed $phone
         */
        public function setPhone($phone)
        {
            $this->_phone = $phone;
        }

        /**
         * @return mixed
         */
        public function getGender()
        {
            return $this->_gender;
        }

        /**
         * @param mixed $gender
         */
        public function setGender($gender)
        {
            $this->_gender = $gender;
        }

        /**
         * @return mixed
         */
        public function getUsername()
        {
            return $this->_username;
        }

        /**
         * @param mixed $username
         */
        public function setUsername($username)
        {
            $this->_username = $username;
        }

        /**
         * @return mixed
         */
        public function getTypes()
        {
            return $this->_types;
        }

        /**
         * @param mixed $types
         */
        public function setTypes($types)
        {
            $this->_types = $types;
        }

        public function getUsersTable() {

            if (!$this->_userTable) {

                $sm = $this->getServiceLocator();


                $this->_userTable = $sm->get('Users\Model\UsersTable');
            }

            return $this->_userTable;
        }


        public function exchangeArray($data)
        {
            $this->setId((!empty($data['userId'])) ? $data['userId'] : null);
            $this->setName((!empty($data['name'])) ? $data['name'] : null);
            $this->setEmail((!empty($data['email'])) ? $data['email'] : null);
            $this->setDoctorId((!empty($data['doctorId'])) ? $data['doctorId'] : null);
            $this->setAddress((!empty($data['address'])) ? $data['address'] : null);
            $this->setDob((!empty($data['dob'])) ? $data['dob'] : null);
            $this->setPhone((!empty($data['phone'])) ? $data['phone'] : null);
            $this->setGender((!empty($data['gender'])) ? $data['gender'] : null);
            $this->setTypes((!empty($data['types'])) ? $data['types'] : null);
            $this->setUsername((!empty($data['username'])) ? $data['username'] : null);
            $this->setDbId((!empty($data['username'])) ? $data['username'] : null);
        }

        public function login($username, $password)
        {
            return $this->getUsersTable()->login($username, $password);
        }

        public function logout()
        {

        }

        public function load($table)
        {
            $select = new Select('users');
            if($this->getTypes() =='P') {
                $select->join(array("p" => "doctor"), "p.doctorId = users.id");

            }else {
                $select->join(array("d" => "patient"), "d.patientId = users.id");

            }
            $select->where("users.id = '{$this->_id}'");

            $resultSet = $table->fetchAll();

        }

        public  static function isLogin()
        {
            $user_session = new Container('user');
            $user_session->offsetGet('login');

            return $user_session->offsetExists('login') && $user_session->offsetGet('login');

        }

    }