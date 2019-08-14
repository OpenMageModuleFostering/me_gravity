<?php
/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Similar
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Similar
 */
class Me_Gravity_Block_Catalog_Product_View_Boxes_Similar extends Me_Gravity_Block_Catalog_Product_View_Boxes_Product
{
    /**
     * @var string
     */
    protected $_boxClass = 'similar';

    /**
     * @var string
     */
    protected $_pageType = 'product';
    
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $this->setRecommendationType(Me_Gravity_Model_Method_Request::PRODUCT_PAGE_SIMILAR);

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Similar Product(s)');
        $this->setRecommendationTitle($boxTitle);

        $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        parent::_construct();
    }
}
