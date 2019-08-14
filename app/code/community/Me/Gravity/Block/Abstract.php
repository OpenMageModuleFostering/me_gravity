<?php
/**
 * Class Me_Gravity_Block_Abstract
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Abstract
 */
class Me_Gravity_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Get extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getGravityHelper()->isFullyEnabled();
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
