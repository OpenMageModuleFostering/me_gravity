<?php
/**
 * Class Me_Gravity_Model_Customers
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Customers
 */
class Me_Gravity_Model_Customers extends Me_Gravity_Model_Export_Customer_Customer
{
    /**
     * Base export headers
     *
     * @var array
     */
    protected $_headers = array(
        'userid'
    );

    /**
     * Generate TSV file
     *
     * @param int $storeId store id
     * @return array|bool
     * @throws Mage_Core_Exception
     */
    public function generateCustomersTsv($storeId = 0)
    {
        $customerCollection = $this->getCustomerCollection($storeId);

        if ($customerCollection->getSize()) {

            $io = new Varien_Io_File();

            $path = Mage::getBaseDir('var') . DS . 'gravity' . DS;
            $name = md5(microtime());
            $file = $path . DS . $name . '.tsv';

            $io->setAllowCreateFolders(true);
            $io->open(array('path' => $path));
            $io->streamOpen($file, 'w+');
            $io->streamLock(true);
            $io->streamWriteCsv($this->getExportHeaders(), "\t");

            foreach ($customerCollection as $customer) {

                $row = array();
                foreach ($this->getExportHeaders() as $attributeCode) {
                    if ($attributeCode == $this->_headers[0]) {
                        $row[] = $customer->getEntityId();
                    } else {
                        $row[] = $this->getAttributeValueByCode($customer, $attributeCode);
                    }
                }

                $io->streamWriteCsv($row, "\t");
            }

            $io->streamUnlock();
            $io->streamClose();

            return array(
                'type' => 'filename',
                'value' => $file,
                'rm' => true // can delete file after use
            );

        } else {

            Mage::throwException($this->_helper->__('Empty customer collection.'));
            return false;
        }
    }

    /**
     * Get export headers
     *
     * @return array
     */
    public function getExportHeaders()
    {
        $exportAttrCodes = $this->getExportAttrCodes();

        if (!empty($this->_attributeValues)) {
            $exportAttrCodes = array_merge($exportAttrCodes, array_keys($this->_attributeValues));
        }

        return array_merge($this->_headers, $exportAttrCodes);
    }

    /**
     * Get additional customer attributes value
     *
     * @param Mage_Customer_Model_Customer $customer      customer
     * @param string                       $attributeCode attribute code
     * @return string
     */
    public function getAttributeValueByCode(Mage_Customer_Model_Customer $customer, $attributeCode = '')
    {
        $customerValue = '';

        if ($attributeCode) {

            if (array_key_exists($attributeCode, $this->_attributeValues)
                && !empty($this->_attributeValues[$attributeCode])
                && $customer->getData($attributeCode)
            ) {
                $customerValue = $this->_attributeValues[$attributeCode][$customer->getData($attributeCode)];
            } elseif ($customer->getData($attributeCode)) {
                $customerValue = $customer->getData($attributeCode);
            }

        }

        return $customerValue;
    }
}
