<?php
/**
 * Class Me_Gravity_Model_System_Config_Source_Layout_Layout
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_System_Config_Source_Layout_Layout
 */
class Me_Gravity_Model_System_Config_Source_Layout_Layout
{
    /**
     * var string
     */
    const LAYOUT_LEFT = 'left';

    /**
     * var string
     */
    const LAYOUT_CONTENT = 'content';

    /**
     * var string
     */
    const LAYOUT_RIGHT = 'right';


    /**
     * Layout option getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = $this->_getGravityHelper();

        return array(
            array('value' => self::LAYOUT_LEFT, 'label'=> $_helper->__('Left Sidebar')),
            array('value' => self::LAYOUT_CONTENT, 'label'=> $_helper->__('Bottom of Content')),
            array('value' => self::LAYOUT_RIGHT, 'label'=> $_helper->__('Right Sidebar'))
        );
    }

    /**
     * Retrieve Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    protected function _getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }
}
