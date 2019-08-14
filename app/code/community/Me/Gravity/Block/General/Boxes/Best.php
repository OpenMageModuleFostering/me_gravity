<?php
/**
 * Class Me_Gravity_Block_General_Boxes_Best
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_General_Boxes_Best
 */
class Me_Gravity_Block_General_Boxes_Best extends Me_Gravity_Block_Recommendation
{
    /**
     * @var string
     */
    protected $_boxClass = 'best';

    /**
     * @var string
     */
    protected $_pageType = 'general';

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
            $this->setRecommendationType(Me_Gravity_Model_Method_Request::GENERAL_PERSONAL_BEST);
            $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        }

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Recommended for you');
        $this->setRecommendationTitle($boxTitle);

        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        parent::_construct();
    }
}
