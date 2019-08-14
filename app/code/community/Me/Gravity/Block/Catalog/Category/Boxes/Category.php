<?php
/**
 * Class Me_Gravity_Block_Catalog_Category_Boxes_Category
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalog_Category_Boxes_Category
 */
class Me_Gravity_Block_Catalog_Category_Boxes_Category extends Me_Gravity_Block_Recommendation
{
    /**
     * @var array|null
     */
    protected $_filters = null;

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
                        'limit' => $this->_recommendationLimit,
                        'filters' => $this->_filters ? $this->_filters : null
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
     * Get all filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Set current category filters
     *
     * @return array
     * @throws Exception
     */
    protected function _setFilters()
    {
        try {

            if (is_null($this->_filters)) {
                $this->_filters['categoryId'] = $this->_getCurrentCategory()->getId();
                $this->_filters = array_merge($this->_filters, $this->getRequest()->getParams());
                if (isset($this->_filters['id'])) {
                    unset($this->_filters['id']);
                }
            }
            return $this->_filters;

        } catch (Mage_Core_Exception $e) {
            $this->getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->getGravityHelper()->__('An error occurred while setting category filters.')
            );
        }
    }

    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', Mage::registry('current_category'));
        }
        return $this->getData('current_category');
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = array();
        }
        return $filters;
    }

    /**
     * Retrieve Layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer(Mage::getSingleton('catalog/layer'));
        }
        return $this->_getData('layer');
    }
}
