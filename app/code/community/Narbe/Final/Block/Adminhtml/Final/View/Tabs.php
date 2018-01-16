<?php

class Narbe_Final_Block_Adminhtml_Final_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Initialize Tabs
     */
    public function __construct() {
        parent::__construct();
        $this->setId('final_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('log')->__('Visitor Info'));
    }

    protected function _beforeToHtml() {
        $this->addTab('tab_url', array(
            'label' => Mage::helper('log')->__('Urls'),
            'title' => Mage::helper('log')->__('Urls'),
            'url' => $this->getUrl('*/*/urls', array('_current' => true)),
            'class' => 'ajax'
        ));

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve visitor entity
     *
     * @return Mage_Log_Model_Visitor
     */
    public function getVisitor() {
        return Mage::registry('current_visitor');
    }

}