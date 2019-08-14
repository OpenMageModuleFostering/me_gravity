<?php
/**
 * Class Me_Gravity_Helper_Boxes
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Helper_Boxes
 */
class Me_Gravity_Helper_Boxes extends Mage_Core_Helper_Data
{
    /**
     * Get box enabled on product page
     *
     * @param string                               $boxName  box name
     * @param string                               $pageType page type
     * @param integer|string|Mage_Core_Model_Store $store    store
     * @return bool
     */
    public function getBoxEnabled($boxName = '', $pageType = '', $store = null)
    {
        if ($boxName && $pageType) {
            return Mage::getStoreConfigFlag('gravity/' . $pageType . '_display/' . $boxName . '_box', $store);
        } else {
            return false;
        }
    }

    /**
     * Get box layout on product page
     *
     * @param string                               $boxName  box name
     * @param string                               $pageType page type
     * @param integer|string|Mage_Core_Model_Store $store    store
     * @return bool
     */
    public function getBoxLayout($boxName = '', $pageType = '', $store = null)
    {
        if ($boxName && $pageType) {
            return Mage::getStoreConfig('gravity/' . $pageType . '_display/' . $boxName . '_box_layout', $store);
        } else {
            return false;
        }

    }

    /**
     * Get box title
     *
     * @param string                               $boxName  box name
     * @param string                               $pageType page type
     * @param integer|string|Mage_Core_Model_Store $store    store
     * @return bool
     */
    public function getBoxTitle($boxName = '', $pageType = '', $store = null)
    {
        if ($boxName && $pageType) {
            return Mage::getStoreConfig('gravity/' . $pageType . '_display/' . $boxName . '_box_title', $store);
        } else {
            return false;
        }
    }

    /**
     * Get box items limit
     *
     * @param string                               $boxName  box name
     * @param string                               $pageType page type
     * @param integer|string|Mage_Core_Model_Store $store    store
     * @return bool
     */
    public function getBoxLimit($boxName = '', $pageType = '', $store = null)
    {
        if ($boxName && $pageType) {
            return Mage::getStoreConfig('gravity/' . $pageType . '_display/' . $boxName . '_box_limit', $store);
        } else {
            return false;
        }
    }

    /**
     * Get box column count
     *
     * @param string                               $boxName  box name
     * @param string                               $pageType page type
     * @param integer|string|Mage_Core_Model_Store $store    store
     * @return bool
     */
    public function getBoxColumns($boxName = '', $pageType = '', $store = null)
    {
        if ($boxName && $pageType) {
            return Mage::getStoreConfig('gravity/' . $pageType . '_display/' . $boxName . '_box_columns', $store);
        } else {
            return false;
        }
    }

    /**
     * Identify recommendation items key by partial string
     *
     * @param array  $bulkItems          bulk items array
     * @param string $recommendationType type
     * @return mixed
     */
    public function identifyBulkKeys($bulkItems = array(), $recommendationType = '')
    {
        $result = array();

        foreach ($bulkItems as $k => $v) {
            if (strpos($k, $recommendationType) !== false) {
                $result[$k] = $bulkItems[$k];
                break;
            }
        }

        return $result;
    }
}
