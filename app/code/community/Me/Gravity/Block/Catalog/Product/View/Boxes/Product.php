<?php
/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Product
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Product
 */
class Me_Gravity_Block_Catalog_Product_View_Boxes_Product extends Me_Gravity_Block_Recommendation
{
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
                        'itemId' => $this->getProduct() ? $this->getProduct()->getId() : null
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
}
