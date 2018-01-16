<?php

class Narbe_Final_Block_Adminhtml_Final_View extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'narbe_final';
        $this->_controller = 'adminhtml_final';
        $this->_mode = 'view';

        $visitor = Mage::registry('current_visitor');

        $this->_headerText = Mage::helper('narbe_final')->__('Visitor Id: %d', $visitor->getId());

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
    }

}