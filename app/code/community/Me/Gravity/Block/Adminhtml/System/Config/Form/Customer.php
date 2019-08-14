<?php
/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Customer
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Customer
 */
class Me_Gravity_Block_Adminhtml_System_Config_Form_Customer extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Return element html
     *
     * @param Varien_Data_Form_Element_Abstract $element element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getButtonHtml();
    }

    /**
     * Return url for button
     *
     * @return string
     */
    public function getExportUrl()
    {
        return Mage::helper('adminhtml')->getUrl(
            'adminhtml/gravity/exportcustomer',
            array('store' => Mage::app()->getRequest()->getParam('store'))
        );
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'gravity_customer_button',
                    'label' => $this->_getGravityHelper()->__('Export Customers'),
                    'onclick' => "setLocation('" . $this->getExportUrl() . "')",
                    'disabled' => !$this->_getGravityHelper()->isFullyEnabled()
                )
            );

        return $button->toHtml();
    }

    /**
     * Get extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    private function _getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }
}
