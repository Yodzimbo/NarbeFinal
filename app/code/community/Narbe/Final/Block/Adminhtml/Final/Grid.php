<?php

class Narbe_Final_Block_Adminhtml_Final_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('finalGrid');
        $this->setDefaultSort('last_visit_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $prefix = Mage::getConfig()->getTablePrefix();

        $collection = Mage::getModel('log/visitor')->getCollection();
        //join to store
        $select = $collection->getSelect();
        $select->join(
            array('store_table'=>$collection->getTable('core/store')),
            '`main_table`.`store_id` = `store_table`.`store_id`',
            array('store_table.group_id AS group_id')
        );
        //join to urls
        $select->join(
            array('url_info_table'=>$prefix.'log_url_info'),
            '`main_table`.`last_url_id` = `url_info_table`.`url_id`',
            array('url_info_table.url AS last_url')
        );

        $this->setCollection($collection);

        //override parent to modify collection parameters
        $lastUrl='';
        $groupId = '';

        $this->_preparePage();

        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        $filter   = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);

            //replace group id with default store id
            $groupId = (int)$data['group_id'];
            if ($groupId){

                unset($data['group_id']);
                $storeGroup = Mage::getModel('core/store_group')->load($groupId);
                $store = $storeGroup->getDefaultStore();
                $storeId = $store->getId();

                $data['store_id'] = $storeId;
            }

            //replace last url
            $lastUrl = $data['last_url'];
            if ($lastUrl){
                unset($data['last_url']);
            }

            $this->_setFilterValues($data);
        }
        else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        }
        else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
            $this->_columns[$columnId]->setDir($dir);
            $this->_setCollectionOrder($this->_columns[$columnId]);
        }

        if (!$this->_isExport) {
            if ($lastUrl){
                $select->where('`url_info_table`.`url` LIKE ?', "%$lastUrl%");
            }

            $this->getCollection()->load();
            $this->_afterLoadCollection();
            //restore group id field
            if ($groupId){
                $data['group_id'] = $groupId;
                $this->_setFilterValues($data);
            }
            //restore last_url field
            if ($lastUrl){
                $data['last_url'] = $lastUrl;
                $this->_setFilterValues($data);
            }
        }

        //$selectStr = (string)$collection->getSelect();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('narbe_final')->__('Visitor ID'),
            'index'        => 'visitor_id',
            'type'        => 'number'
        ));

        $this->addColumn('session_id', array(
            'header'    => Mage::helper('narbe_final')->__('Session ID'),
            'align'     => 'left',
            'index'     => 'session_id',
        ));

        $this->addColumn('first_visit_at', array(
            'header'    => Mage::helper('narbe_final')->__('First Visit At'),
            'index'     => 'first_visit_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));

        $this->addColumn('client_ip', array(
            'header'    => Mage::helper('core/http')->__('CLient IP'),
            'index'     => 'client_ip',
            'width'     => '120px'
        ));

        $this->addColumn('last_visit_at', array(
            'header'    => Mage::helper('narbe_final')->__('Last Visit At'),
            'index'     => 'last_visit_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));

        $this->addColumn('group_id', array(
            'header'    => Mage::helper('narbe_final')->__('Store'),
            'align'     => 'left',
            //'sortable'	=> false,
            'index'     => 'group_id',
            'type'		=> 'text'
        ));

        $this->addColumn('store_id', array(
            'header'    => Mage::helper('narbe_final')->__('Store View'),
            'align'     => 'left',
            //'sortable'	=> false,
            'index'     => 'store_id',
            'type'		=> 'store'
        ));

        $this->addColumn('last_url', array(
            'header'    => Mage::helper('narbe_final')->__('Last Url'),
            'align'     => 'left',
            'sortable'	=> true,
            'index'     => 'last_url',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('narbe_final')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('narbe_final')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('narbe_final')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addStoreFilter($value);
        return $this;
    }
}
