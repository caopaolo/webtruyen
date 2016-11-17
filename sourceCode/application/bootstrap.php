<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoload() {
        $autoLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH,
        ));
        return $autoLoader;
    }

    // Load lai namspace neu chua co
    protected function _initAutoload2() {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->setFallbackAutoloader(true);
        return $autoLoader;
    }
    
    protected function _initLoadPlugin() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
            'module' => 'error',
            'controller' => 'error',
            'action' => 'error'
        )));
        return $front;
    }

    protected function _initDatabase() {
        // Set up DB1
        $dbOptionds = $this->getOption('resources');
        $dbOptionds = $dbOptionds['db'];
        $db = Zend_Db::factory($dbOptionds['adapter'], $dbOptionds['params']);
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);

        Zend_Registry::set('connectDB', $db);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
      
        // Set up DB2
        $params2 = array(
            'host'           => 'localhost',
            'port'           => '3306',
            'username'       => 'root',
            'password'       => '',
            'dbname'         => 'amobi_vn',
            'charset'        => 'utf8'
        );
         
        $db2 = Zend_Db::factory('Pdo_Mysql', $params2);
        $db2->setFetchMode(Zend_Db::FETCH_ASSOC);
        Zend_Registry::set('connectDB2', $db2);
        // return it, so that it can be stored in bootstrap
        return $db;
    }
    
    // Khoi tao hang so
    protected function _initSetConstants() {
        // Them file cau hinh cac hang so su dung trong project
        $config = parse_ini_file(APPLICATION_PATH . '/configs/constant.ini');
        foreach($config as $key=>$value){
            // neu chua dinh nghia thi moi dinh nghia lai
            if(!defined($key)){
                define($key, $value);
            }
        }
    }
    
}