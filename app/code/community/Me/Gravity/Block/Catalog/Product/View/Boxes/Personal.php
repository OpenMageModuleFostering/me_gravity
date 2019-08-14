<?php
/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Personal
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalog_Product_View_Boxes_Personal
 */
class Me_Gravity_Block_Catalog_Product_View_Boxes_Personal extends Me_Gravity_Block_Catalog_Product_View_Boxes_Product
{
    /**
     * @var string
     */
    protected $_boxClass = 'personal';

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
        $gravityHelper = $this->getGravityHelper();

        if (!$gravityHelper->useBulkRecommendation() || $gravityHelper->useGravityTemplate()) {
            $this->setRecommendationType(Me_Gravity_Model_Method_Request::PRODUCT_PAGE_PERSONAL);
            $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        }

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Personal Product(s)');
        $this->setRecommendationTitle($boxTitle);


        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        parent::_construct();
    }
}
