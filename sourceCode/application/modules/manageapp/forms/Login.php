<?php

class Manageapp_Forms_Login extends Zend_Form {

    public function init() {
        $this->setName("login");
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Tên đăng nhập:',    
        ));

        $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required' => true,
            'label' => 'Mật khẩu:',
        ));

        $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Đăng nhập',
            'class' => 'btn btn-large btn-primary',
        ));
    }

}
