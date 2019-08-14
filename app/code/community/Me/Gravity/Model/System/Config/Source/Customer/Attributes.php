<?php
/**
 * Class Me_Gravity_Model_System_Config_Source_Customer_Attributes
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_System_Config_Source_Customer_Attributes
 */
class Me_Gravity_Model_System_Config_Source_Customer_Attributes
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        $exportDefault = Mage::getModel('me_gravity/export_customer_customer');
        $options[] = array('label' => '', 'value' => '');
        foreach ($exportDefault->getCustomerAttributeCollection() as $attribute) {

            if (!in_array($attribute->getFrontendInput(), $exportDefault->getExcludedAttributeTypes())
                && !in_array($attribute->getAttributeCode(), $exportDefault->getExportAttrCodes())
                && !in_array($attribute->getAttributeCode(), $exportDefault->getExcludeAttributeCodes())
                && $attribute->getFrontendInput()
            ) {
                $options[] = array(
                    'label' => Mage::helper('customer')->__($attribute['frontend_label']),
                    'value' => $attribute['attribute_code']
                );
            }
        }
        return $options;
    }
}
