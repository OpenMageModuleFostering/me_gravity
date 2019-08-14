<?php
/**
 * Class Me_Gravity_Block_Checkout_Cart_Boxes_Cart
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Checkout_Cart_Boxes_Cart
 */
class Me_Gravity_Block_Checkout_Cart_Boxes_Cart extends Me_Gravity_Block_Recommendation
{
    /**
     * @var string
     */
    protected $_boxClass = 'cart';

    /**
     * @var string
     */
    protected $_pageType = 'cart';

    /**
     * @var array
     */
    protected $_itemsInCart = array();

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $this->setRecommendationType(Me_Gravity_Model_Method_Request::CART_PAGE);

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Personal Product(s)');
        $this->setRecommendationTitle($boxTitle);

        $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        $this->_itemsInCart = $this->_getProductIdsInCart();

        parent::_construct();
    }

    /**
     * Get cart items
     *
     * @return $this|bool
     */
    public function getProductCollection()
    {
        if ($this->getRecommendationType()) {

            $items = Mage::getModel('me_gravity/method_request')->sendRequest(
                Me_Gravity_Model_Method_Request::EVENT_TYPE_GET,
                array(
                    'type' => $this->_recommendationType,
                    'limit' => $this->_recommendationLimit,
                    'itemsInCart' => $this->_itemsInCart ? $this->_itemsInCart : null
                )
            );

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
     * Get cart product ids
     *
     * @return array
     */
    public function getCartItems()
    {
        return $this->_itemsInCart;
    }
}
