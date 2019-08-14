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
class Me_Gravity_Block_Adminhtml_System_Config_Form_Catalog extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Path to store config catalog export file save path
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_PATH = 'gravity/export/catalog_path';

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
            'adminhtml/gravity/exportcatalog',
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
                    'id' => 'gravity_catalog_button',
                    'label' => $this->_getGravityHelper()->__('Export Products'),
                    'onclick' => "setLocation('" . $this->getExportUrl() . "')",
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
        if ($this->_getGravityHelper()->isFullyEnabled() && $this->_getCatalogFilePath()) {
            return false;
        } else {
            return true;
        }
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

    /**
     * Get catalog export file path
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    private function _getCatalogFilePath($store = null)
    {
        return Mage::getStoreConfig(self::XML_CATALOG_EXPORT_PATH, $store);
    }
}
