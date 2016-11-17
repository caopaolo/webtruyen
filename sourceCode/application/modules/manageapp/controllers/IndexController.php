<?php

class Manageapp_IndexController extends Amobi_Controller_Action {

    protected $_layout = 'manageapp'; // Bien luu layout cua controller
    
    public function indexAction () {
        $this->_redirect('/manageapp/category');
    }
}
