<?php
/**
 * Class Me_Gravity_Block_Widget_Boxes_Widget
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Widget_Boxes_Widget
 * @method string getEventType()
 * @method string getBoxTitle()
 * @method int getBoxLimit()
 * @method int getBoxColumns()
 */
class Me_Gravity_Block_Widget_Boxes_Widget extends Me_Gravity_Block_Recommendation implements Mage_Widget_Block_Interface
{
    /**
     * @var string
     */
    protected $_boxClass = 'widget';

    /**
     * Allowed types
     *
     * @var array
     */
    protected $_allowedTypes = array(
        'personal_best',
        'personal_history',
        'currently_viewed',
        'popular'
    );

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $boxHelper = $this->_getGravityBoxHelper();

        if (!$this->setRecommendationType($this->getEventType())) {
            return;
        }

        $this->setTemplate('me/gravity/widget/boxes/widget.phtml');
        $this->setRecommendationTitle($this->getBoxTitle());
        $this->setRecommendationLimit($this->getBoxLimit());
        $this->setBoxColumnCount($this->getBoxColumns());

        parent::_construct();
    }

    /**
     * Set type parameter
     *
     * @param string $type recommendation type
     * @return string|void
     */
    public function setRecommendationType($type = '')
    {
        if (in_array($type, $this->_allowedTypes)) {
            $this->_recommendationType = $type;

            return $this->_recommendationType;
        } else {
            return false;
        }
    }

    /**
     * Set item limit
     *
     * @param int $limit limit
     * @return int|void
     */
    public function setRecommendationLimit($limit = 10)
    {
        if (intval($limit)) {
            $this->_recommendationLimit = $limit;
        } else {
            return 5;
        }
    }

    /**
     * Set box column count
     *
     * @param int $columns column count
     * @return int|void
     */
    public function setBoxColumnCount($columns = 4)
    {
        if (intval($columns)) {
            $this->_columnCount = $columns;
        } else {
            return 4;
        }
    }
}
