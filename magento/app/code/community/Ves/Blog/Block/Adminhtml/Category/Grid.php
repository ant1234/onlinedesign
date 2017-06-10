<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {

        parent::__construct();


        $this->setId('categoryGrid');
        $this->setDefaultSort('date_from');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ves_blog/category')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns() {

        $this->addColumn('category_id', array(
            'header'    => Mage::helper('ves_blog')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'category_id',
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id',
                array (
                    'header' => Mage::helper('cms')->__('Store view'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => array (
                        $this,
                        '_filterStoreCondition' ) ));
        }

        $this->addColumn('title', array(
            'header'    => Mage::helper('ves_blog')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
            ));
        $this->addColumn('identifier', array(
            'header'    => Mage::helper('ves_blog')->__('Identifier'),
            'align'     =>'left',
            'index'     => 'identifier',
            ));
        $this->addColumn('file', array(
            'header'    => Mage::helper('ves_blog')->__('Image'),
            'align'     =>'left',
            'index'     => 'file',
            ));
        $this->addColumn('position', array(
            'header'    => Mage::helper('ves_blog')->__('Sort Order'),
            'align'     =>'left',
            'index'     => 'position',
            'width'     => '80px',
            ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('ves_blog')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('ves_blog')->__('Enabled'),
                0 => Mage::helper('ves_blog')->__('Disabled'),
                //3 => Mage::helper('ves_blog')->__('Hidden'),
                ),
            ));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        return parent::_prepareColumns();
    }
    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('ves_blog')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('ves_blog')->__('Are you sure?')
            ));

        $statuses = array(
            1 => Mage::helper('ves_blog')->__('Enabled'),
            2 => Mage::helper('ves_blog')->__('Disabled')
            );
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('ves_blog')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('ves_blog')->__('Status'),
                    'values' => $statuses
                    )
                )
            ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}