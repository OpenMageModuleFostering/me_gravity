<?php
/**
 * Class Me_Gravity_Block_Page_Js_Gravity
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Page_Js_Gravity
 */
class Me_Gravity_Block_Page_Js_Gravity extends Me_Gravity_Block_Abstract
{
    /**
     * Get Gravity customer Id
     *
     * @return string
     */
    public function getGravityCustomerId()
    {
        return $this->getGravityHelper()->getApiUser();
    }

    /**
     * Get current customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }

    /**
     * Get custom Gravity JS is enabled
     *
     * @return bool
     */
    public function isCustomJsEnabled()
    {
        return $this->getGravityHelper()->getIsCustomJsEnabled();
    }

    /**
     * Get custom Gravity JS content
     *
     * @return string
     */
    public function getCustomJsContent()
    {
        return $this->getGravityHelper()->getCustomJs();
    }
}
