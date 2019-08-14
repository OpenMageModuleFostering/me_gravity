<?php
/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Catalog
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Adminhtml_System_Config_Form_Catalog
 */
class Me_Gravity_Block_Adminhtml_System_Config_Form_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Return element html
     *
     * @param Varien_Data_Form_Element_Abstract $element element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_getExtensionVersion();
    }

    /**
     * Get extension version number
     *
     * @return string
     */
    private function _getExtensionVersion()
    {
        return (string) Mage::getConfig()->getNode()->modules->Me_Gravity->version;
    }
}
