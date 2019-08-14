<?php
/**
 * Class Me_Gravity_Block_Catalog_Category_Boxes_Top
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalog_Category_Boxes_Top
 */
class Me_Gravity_Block_Catalog_Category_Boxes_Top extends Me_Gravity_Block_Catalog_Category_Boxes_Category
{
    /**
     * @var string
     */
    protected $_boxClass = 'top';

    /**
     * @var string
     */
    protected $_pageType = 'category';

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
            $this->setRecommendationType(Me_Gravity_Model_Method_Request::CATEGORY_PAGE_TOP);
            $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        }

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Top Product(s)');
        $this->setRecommendationTitle($boxTitle);

        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        $this->_setFilters();

        parent::_construct();
    }
}
