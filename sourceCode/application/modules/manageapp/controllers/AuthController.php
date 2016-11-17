<?php

class Manageapp_AuthController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        try {
            $request = $this->getRequest();
            $values['username'] = $this->_request->getParam('username');
            $values['password'] = $this->_request->getParam('password');
            if (!empty($values['username']) && !empty($values['password'])) {
                if ($this->_process($values)) {
                  
                    $this->_redirect('/manageapp/category');
                }
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    protected function _process($values) {
        $session = new Zend_Auth_Storage_Session('Zend_Auth_ManageApp');
        
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['username']);
        $adapter->setCredential($values['password']);
        $auth = Zend_Auth::getInstance();
        $auth->setStorage($session);       
    
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
           
            $auth->getStorage()->write($user);    
           
            return true;
        }
        return false;
    }

    protected function _getAuthAdapter() {
        $dbAdapter = Zend_Registry::get('connectDB');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('admin')
                ->setIdentityColumn('user_name')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('SHA1(?)');       
        return $authAdapter;
    }

    public function logoutAction() {
        try {            
            // Xoa du lieu dang nhap
            $auth = Zend_Auth::getInstance();
            $auth->setStorage(new Zend_Auth_Storage_Session('Zend_Auth_ManageApp'));
        	$auth->clearIdentity();
            
            // Tao lai session de chong session fixation
            Zend_Session::destroy();
            Zend_Session::regenerateId();
            
            // Back to login page
            $this->_helper->redirector('index');    
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }
}