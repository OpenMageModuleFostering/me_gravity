<?php
/**
 * Class Me_Gravity_Block_Recommendation
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Recommendation
 */
class Me_Gravity_Block_Recommendation extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection;

    /**
     * @var int
     */
    protected $_columnCount = 4;

    /**
     * @var string
     */
    protected $_recommendationType;

    /**
     * @var string
     */
    protected $_recommendationId;

    /**
     * @var string
     */
    protected $_recommendationTitle = '';

    /**
     * @var int
     */
    protected $_recommendationLimit = 10;

    /**
     * @var string
     */
    protected $_boxClass = '';

    protected $_groupSec = 1;

    /**
     * Get recommended items
     *
     * @return $this|bool
     */
    public function getProductCollection()
    {
        if ($this->getRecommendationType()) {

            $items = array();

            if (!$this->getGravityHelper()->useBulkRecommendation()) {
                $items = Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_GET,
                    array(
                        'type' => $this->_recommendationType,
                        'limit' => $this->_recommendationLimit
                    )
                );
            } else {
                $items = $this->getBulkItems();
            }

            if (!empty($items)) {

                $this->_recommendationId = key($items);
                $itemCollection = $this->_getProductCollection();
                $itemCollection->addAttributeToFilter('entity_id', array('in' => $items));
                $itemCollection->load();

            } else {

                $itemCollection = null;

            }

            return $itemCollection;

        } else {

            return null;

        }
    }

    /**
     * Prepare product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {

            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter();

            $this->_addProductAttributesAndPrices($collection);

            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Get collection before rendering
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if (!$this->getGravityHelper()->useGravityTemplate()) {
            $this->_getProductCollection();
        }
        return parent::_beforeToHtml();
    }

    /**
     * Get recommendation id
     *
     * @return string
     */
    public function getRecommendationId()
    {
        return $this->_recommendationId;
    }

    /**
     * Set type parameter
     *
     * @param string $type recommendation type
     * @return string
     */
    public function setRecommendationType($type = '')
    {
        $this->_recommendationType = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getRecommendationType()
    {
        return $this->_recommendationType;
    }

    /**
     * Set box title
     *
     * @param string $title box title
     * @return void
     */
    public function setRecommendationTitle($title = '')
    {
        $this->_recommendationTitle = $title;
    }

    /**
     * Get box title
     *
     * @return string
     */
    public function getRecommendationTitle()
    {
        return $this->_recommendationTitle;
    }

    /**
     * Set item limit
     *
     * @param int $limit item limit
     * @return void
     */
    public function setRecommendationLimit($limit = 10)
    {
        $this->_recommendationLimit = $limit;
    }

    /**
     * Get item limit
     *
     * @return string
     */
    public function getRecommendationLimit()
    {
        return $this->_recommendationLimit;
    }

    /**
     * Set box column count
     *
     * @param int $columns column count
     * @return void
     */
    public function setBoxColumnCount($columns = 4)
    {
        $this->_columnCount = $columns;
    }

    /**
     * Get box column count
     *
     * @return int
     */
    public function getBoxColumnCount()
    {
        return $this->_columnCount;
    }

    /**
     * Get box class
     *
     * @return string
     */
    public function getBoxClass()
    {
        return $this->_boxClass;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * Get bulk items
     *
     * @return mixed
     */
    public function getBulkItems()
    {
        return $this->getData('bulk_items');
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get product ids in cart
     *
     * @return array
     */
    protected function _getProductIdsInCart()
    {
        return Mage::getSingleton('checkout/cart')->getProductIds();
    }

    /**
     * Retrieve Gravity boxes helper
     *
     * @return Me_Gravity_Helper_Boxes
     */
    protected function _getGravityBoxHelper()
    {
        return Mage::helper('me_gravity/boxes');
    }

    /**
     * Get Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    public function getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }
}
