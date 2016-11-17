<?php

class Amobi_Controller_Api extends Zend_Controller_Action{
	
    protected $_arrParam;
    protected $_appInfo;
    protected $_cache;
    protected $_ip;
    protected $_host;
    
    public function init() {
        // Lay tham so truyen len
        $this->_arrParam = $this->_request->getParams();
        
        // Bo di cac truong ko can thiet
        unset($this->_arrParam['module']);
        unset($this->_arrParam['controller']);
        unset($this->_arrParam['action']);
        
        // Loc cac bien dau vao
        foreach ($this->_arrParam as &$p) {
            $p = Amobi_Utilities_Input::standardInputString($p);
        }
        
        // Tao cache
        $this->_cache = $this->__createCache();
        
        $this->_helper->viewRenderer->setNoRender('true');
        
        // Get Ip
        $ipUtility = new Amobi_Utilities_Ip();
        $this->_ip = $ipUtility->getIpAddress();
        
        // Get Host
        $this->_host = $this->view->host = Amobi_Utilities_Server::getHost();
    }
    
    /********************************* Begin: Cac ham ho tro *******************************/
    
    // Ham show loi
    protected function __error($message) {
        $response= array(
            'status'    => 0,
            'message'   => $message
        );
        $this->__showJson($response);
    }
    
    // Ham show du lieu theo dinh dang json
    protected function __showJson($data, $jsonEncode = 1){
        if ($jsonEncode == 1) {
            $data = json_encode($data);    
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($data);
        return;
    }
    
    // Ham tao cache
    protected function __createCache() {
        try {
            $frontendOptions = array(
                'lifetime' => 300,
                'automatic_serialization' => true
            );

            $backendOptions = array(
                'cache_dir' => APPLICATION_PATH . '/cache/' // Directory where to put the cache files
            );

            // getting a Zend_Cache_Core object
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            return $cache;
        } catch (Exception $e) {
            return false;
            //return $e->getMessage();
        }
    }
    
    // Ham kiem tra dang nhap
    protected function __checklogin($app_id) {
        try {
            // Chuan bi tham so
            $commentModel = new Model_api_generalapp_Comment();
            
            if (!empty($_COOKIE['user_token'])) {
                $check = $commentModel->checkLogin($_COOKIE['user_token'], $app_id);
                if (!empty($check)) {
                    $user_info = $commentModel->getUser($_COOKIE['user_token']);
                    $response = array(
                        'status'    => 1,
                        'info'      => $user_info
                    );
                } else {
                    $response = array(
                        'status'    => 0,
                        'info'      => "Don't login"
                    );
                }
            } else {
                $response = array(
                    'status'    => 0,
                    'info'      => "Don't login"
                );
            }
        } catch (Exception $exc) {
            $response = array(
                'status'    => 0,
                'info'      => $exc->getMessage()
            );
        }
        return $response;
    }
    
    /********************************* End: Cac ham ho tro *******************************/
}