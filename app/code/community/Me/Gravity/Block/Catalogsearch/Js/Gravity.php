<?php
/**
 * Class Me_Gravity_Block_Catalogsearch_Js_Gravity
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalogsearch_Js_Gravity
 */
class Me_Gravity_Block_Catalogsearch_Js_Gravity extends Mage_CatalogSearch_Block_Result
{
    /**
     * Get extension is properly enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getGravityHelper()->isFullyEnabled();
    }

    /**
     * Get search keywords
     *
     * @return string
     */
    public function getSearchKeyword()
    {
        return $this->helper('catalogsearch')->getEscapedQueryText();
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
     * Get current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
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
