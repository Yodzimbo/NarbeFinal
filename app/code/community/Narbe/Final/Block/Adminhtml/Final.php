<?php

class Narbe_Final_Block_Adminhtml_Final extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller         = 'adminhtml_final';
        $this->_blockGroup         = 'narbe_final';
        parent::__construct();
        $this->_headerText         = Mage::helper('narbe_final')->__('Narbe Final');
        $this->removeButton('add');
    }
}