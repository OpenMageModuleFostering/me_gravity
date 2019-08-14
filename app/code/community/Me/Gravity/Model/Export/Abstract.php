<?php
/**
 * Class Me_Gravity_Model_Export_Abstract
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Export_Abstract
 */
abstract class Me_Gravity_Model_Export_Abstract
{
    /**
     * @var Me_Gravity_Helper_Data
     */
    protected $_helper;

    /**
     * Base attributes to export
     *
     * @var array
     */
    protected $_baseAttributeCodes = array();

    /**
     * Disabled attribute types
     *
     * @var array
     */
    protected $_excludedAttributesTypes = array();

    /**
     * Disabled attribute codes
     *
     * @var array
     */
    protected $_excludedAttributeCodes = array();

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array();

    /**
     * Attribute code to its values. Only attributes with options and only default store values used.
     *
     * @var array
     */
    protected $_attributeValues = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('me_gravity');
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('catalog/product_attribute_collection');
    }

    /**
     * Customer entity attributes collection getter.
     *
     * @return Mage_Customer_Model_Resource_Attribute_Collection
     */
    public function getCustomerAttributeCollection()
    {
        return Mage::getResourceModel('customer/attribute_collection');
    }

    /**
     * Get attributes to filter
     *
     * @return array
     */
    public function getExportAttrCodes()
    {
        return $this->_baseAttributeCodes;
    }

    /**
     * Get excluded attribute types
     *
     * @return array
     */
    public function getExcludedAttributeTypes()
    {
        return $this->_excludedAttributesTypes;
    }

    /**
     * Get excluded attribute codes
     *
     * @return array
     */
    public function getExcludeAttributeCodes()
    {
        return $this->_excludedAttributeCodes;
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute attribute
     * @return array
     */
    public function getAttributeOptions(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $options = array();

        if ($attribute->usesSource()) {

            $index = in_array($attribute->getAttributeCode(), $this->_indexValueAttributes) ? 'value' : 'label';

            foreach (Mage::app()->getStores() as $store) {
                $attribute->setStoreId($store->getId());

                try {
                    foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                        foreach (is_array($option['value']) ? $option['value'] : array($option) as $innerOption) {
                            if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                                $options[$store->getId()][$innerOption['value']] = $innerOption[$index];
                            }
                        }
                    }
                } catch (Exception $e) {
                    // ignore exceptions connected with source models
                }
            }
        }

        return $options;
    }
}
