<?php
/**
 * Class Me_Gravity_Model_System_Config_Source_Layout_Pages
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_System_Config_Source_Layout_Pages
 */
class Me_Gravity_Model_System_Config_Source_Layout_Pages
{
    /**
     * Layout option getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        $_helper = $this->_getGravityHelper();
        $options[] = array('label' => '', 'value' => '');
        $options = array_merge($options, Mage::getSingleton('adminhtml/system_config_source_cms_page')->toOptionArray());
        $additionalOptions = array(
            array('value' => 'customer_account_index', 'label'=> $_helper->__('Account Dashboard')),
            array('value' => 'contacts_index_index', 'label'=> $_helper->__('Contact Us'))
        );
        $options = array_merge($options, $additionalOptions);

        return $options;
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
