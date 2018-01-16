<?php


class Narbe_Final_Adminhtml_FinalController extends Mage_Adminhtml_Controller_Action
{
    protected function _initVisitor($idFieldName = 'id') {
        $this->_title($this->__('Visitor Info'))->_title($this->__('Visitor Log'));
        $visitorId = (int) $this->getRequest()->getParam($idFieldName);

        $visitor = Mage::getModel('log/visitor');

        if ($visitorId) {
            $visitor->load($visitorId);
        }

        Mage::register('current_visitor', $visitor);
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Visitors'))->_title($this->__('Visitor Log'));
        $this->loadLayout()->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }

    public function viewAction() {
        $this->_initVisitor();
        $this->loadLayout()->renderLayout();
    }

    public function urlsAction() {
        $this->_initVisitor();
        $this->loadLayout()->renderLayout();
    }

    public function exportCsvAction() {
        $fileName = 'final.csv';
        $content = $this->getLayout()->createBlock('narbe_final/adminhtml_final_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction() {
        $fileName = 'narbe_final.xls';
        $content = $this->getLayout()->createBlock('narbe_final/adminhtml_final_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'narbe_final.xml';
        $content = $this->getLayout()->createBlock('narbe_final/adminhtml_final_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}