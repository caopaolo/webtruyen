<?php

class Amobi_Controller_Action extends Zend_Controller_Action{
	
    protected $_layout = '';
    protected $_host;
    protected $_arrParam;
    protected $_action;
    protected $_controller;
    protected $_module;
    protected $_userInfo;
    protected $_app;
    protected $_role;
    protected $_publishedAction = 0;

	public function init() {
        try {
            // Lay dia chi host
            $pageURL = 'http';
            if (!empty($_SERVER['HTTPS'])) {
                if ($_SERVER['HTTPS'] == 'on') {
                    $pageURL .= "s";
                }
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"];
            }
            $this->_host = $this->view->host = $pageURL;
    
            // Lay tham so truyen len
            $this->_arrParam = $this->_request->getParams();
            
            // Bo di cac truong ko can thiet
            unset($this->_arrParam['module']);
            unset($this->_arrParam['controller']);
            unset($this->_arrParam['action']);
    
            // Them vao view cac chung
            $this->view->controller = $this->_controller = $this->_request->getControllerName();
            $this->view->action = $this->_action = $this->_request->getActionName();
            $this->view->module = $this->_module = $this->_request->getModuleName();
            
            // Loc cac bien dau vao
            foreach ($this->_arrParam as &$p) {
                $p = Amobi_Utilities_Input::standardInputString($p);
            }
            // Truyen tham so ra view
            $this->view->arrParam = $this->_arrParam;
            
            // Khoi tao layout
            if (!empty($this->_layout)) {
                $option = array(
                    "layout"        => $this->_layout,
                    "layoutPath"    => APPLICATION_PATH . "/layouts/scripts/"
                );
                Zend_Layout::startMvc($option);    
            }
            $this->view->pageTitle = "Content manager";
        } catch (Exception $exc) {
            echo $exc->getMessage(); die;
        }
    }

    public function preDispatch() {
        try {
            // Kiem tra dang nhap
            if (strcmp($this->_action, 'auth') != 0) {
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('Zend_Auth_ManageApp'));
                if (!$auth->hasIdentity()) {
                    $this->_redirect('/manageapp/auth');
                } else {
                    // Kiem tra phan quyen
                    $authModel = new Model_api_manageapp_Auth();
                    $this->_userInfo = $auth->getIdentity();
                   
                
                    
                    // Khoi tao bien
                   
                    $params = $this->_arrParam;
                    
                    
                    
                    // Truyen bien ra view
             
                    $this->view->username = $this->_userInfo->user_name;
                  
                  
                   
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage(); die;
        }
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

    public function checkAppPublished(){
        $this->view->published = $this->isPublished();
        if(!$this->view->published){
            $this->_redirect('/manageapp/content');
        }
    }


    
    /********************************* End: Cac ham ho tro *******************************/
}