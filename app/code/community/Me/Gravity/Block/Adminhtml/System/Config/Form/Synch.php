<?php
/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Test
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila S�gi <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Test
 */
class Me_Gravity_Block_Adminhtml_System_Config_Form_Synch extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('me/gravity/system/config/form/synch.phtml');
    }

    /**
     * Return element html
     *
     * @param Varien_Data_Form_Element_Abstract $element element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxTestUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/gravity/test');
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
                    'id' => 'gravity_test_button',
                    'label' => $this->_getGravityHelper()->__('Test Connection'),
                    'onclick' => 'javascript:check(); return false;',
                    'disabled' => $this->_getIsButtonEnabled()
                )
            );

        return $button->toHtml();
    }

    /**
     * Check if button enabled
     *
     * @return bool
     */
    private function _getIsButtonEnabled()
    {
        if ($this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    public function _getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }
}
