<?php
/**
 * Class Me_Gravity_Model_Export_Customer_Customer
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Export_Customer_Customer
 */
class Me_Gravity_Model_Export_Customer_Customer extends Me_Gravity_Model_Export_Abstract
{
    /**
     * Maximum customers to export
     *
     * @var int
     */
    const MAX_LIMIT = 1000;

    /**
     * Base attributes to export
     *
     * @var array
     */
    protected $_baseAttributeCodes = array(
        'email',
        'firstname',
        'lastname'
    );

    /**
     * Disabled attribute codes
     *
     * @var array
     */
    protected $_excludedAttributeCodes = array(
        'confirmation',
        'created_at',
        'created_in',
        'default_billing',
        'default_shipping',
        'disable_auto_group_change',
        'password_hash',
        'reward_update_notification',
        'reward_warning_notification',
        'rp_token',
        'rp_token_created_at'
    );

    /**
     * @var Mage_Customer_Model_Resource_Customer_Collection
     */
    protected $_customerCollection;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initAttributes();
    }

    /**
     * Export product collection
     *
     * @param int $storeId store id
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function getCustomerCollection($storeId = 0)
    {
        if (is_null($this->_customerCollection)) {

            $this->_customerCollection = $this->_addBaseAttributes($this->_getCustomerCollection())
                ->addAttributeToFilter('website_id', Mage::app()->getStore($storeId)->getWebsiteId());

            if (!$this->_helper->getCustomerExportAll()) {
                if ($this->_helper->getCustomerMaxLimit()) {
                    $this->_customerCollection->setPageSize((int)$this->_helper->getCustomerMaxLimit());
                } else {
                    $this->_customerCollection->setPageSize(self::MAX_LIMIT);
                }
            }

            $this->_customerCollection->load();

        }

        return $this->_customerCollection;
    }

    /**
     * Get base customer collection
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected function _getCustomerCollection()
    {
        return Mage::getResourceModel('customer/customer_collection');
    }

    /**
     * Add base attributes to collection
     *
     * @param Mage_Customer_Model_Resource_Customer_Collection $collection collection
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected function _addBaseAttributes(Mage_Customer_Model_Resource_Customer_Collection $collection)
    {
        $exportAttrCodes = $this->getExportAttrCodes();

        if (!empty($this->_attributeValues)) {
            $exportAttrCodes = array_merge($exportAttrCodes, array_keys($this->_attributeValues));
        }

        foreach ($this->getCustomerAttributeCollection() as $attribute) {

            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, $exportAttrCodes) && !in_array($attributeCode, $this->_excludedAttributeCodes)) {
                $collection->addAttributeToSelect($attributeCode);
            }

        }

        return $collection;
    }

    /**
     * Initialize attribute option values and types
     *
     * @return $this
     */
    protected function _initAttributes()
    {
        if ($additionalAttributes = $this->_helper->getAdditionalCustomerAttributes()) {
            foreach ($this->getCustomerAttributeCollection() as $attribute) {
                if (in_array($attribute->getAttributeCode(), $additionalAttributes)) {
                    $this->_attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
                }
            }
        }

        return $this;
    }
}
