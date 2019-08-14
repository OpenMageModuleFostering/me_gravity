<?php
/**
 * Class Me_Gravity_Helper_Data
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Helper_Data
 */
class Me_Gravity_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Extension log filename
     *
     * @var string
     */
    protected $_logfile = 'gravity.log';

    /**
     * Path to store config if extension is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED = 'gravity/config/enabled';

    /**
     * Path to store config if preview mode is enabled
     *
     * @var string
     */
    const XML_PATH_PREVIEW = 'gravity/config/preview';

    /**
     * Path to store config if debug mode is enabled
     *
     * @var string
     */
    const XML_PATH_DEBUG = 'gravity/debug/log';

    /**
     * Path to store config API username
     *
     * @var string
     */
    const XML_PATH_API_USER = 'gravity/config/api_user';

    /**
     * Path to store config API password
     *
     * @var string
     */
    const XML_PATH_API_PASSWORD = 'gravity/config/api_password';

    /**
     * Path to store config if custom Gravity JS enabled
     *
     * @var string
     */
    const XML_PATH_CUSTOM_JS_ENABLED = 'gravity/config/enabled_js';

    /**
     * Path to store config custom Gravity JS
     *
     * @var string
     */
    const XML_PATH_CUSTOM_JS = 'gravity/config/custom_js';

    /**
     * Path to store config catalog export path
     *
     * @var string
     */
    const XML_PATH_EXPORT_PATH = 'gravity/export/catalog_path';

    /**
     * Path to store config use Gravity template
     *
     * @var string
     */
    const XML_PATH_USE_TEMPLATE = 'gravity/config/template';

    /**
     * Path to store config use bulk recommendation
     *
     * @var string
     */
    const XML_PATH_USE_BULK = 'gravity/config/bulk';

    /**
     * Path to store config catalog export all
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_ALL_PATH = 'gravity/export/all';

    /**
     * Path to store config catalog export maximum
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_MAX_PATH = 'gravity/export/max';

    /**
     * Path to store config if product's description enabled in catalog export
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_DESCRIPTION = 'gravity/export/description_enabled';

    /**
     * Path to store config catalog export cron enabled
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_CRON = 'gravity/export/catalog_cron';

    /**
     * Path to store config customer export all
     *
     * @var string
     */
    const XML_CUSTOMER_EXPORT_ALL_PATH = 'gravity/customer_export/all';

    /**
     * Path to store config customer export maximum
     *
     * @var string
     */
    const XML_CUSTOMER_EXPORT_MAX_PATH = 'gravity/customer_export/max';

    /**
     * Path to store config additional catalog attributes for export
     *
     * @var string
     */
    const XML_PATH_EXPORT_CATALOG_ADDITIONAL = 'gravity/export/additional';

    /**
     * Path to store config additional customer attributes for export
     *
     * @var string
     */
    const XML_PATH_EXPORT_CUSTOMER_ADDITIONAL = 'gravity/cutomer_export/additional';

    /**
     * Path to store config only salable parameter for export
     *
     * @var string
     */
    const XML_PATH_EXPORT_ONLY_SALABLE = 'gravity/export/only_salable';

    /**
     * Path to store config product status parameter for export
     *
     * @var string
     */
    const XML_PATH_EXPORT_STATUS = 'gravity/export/status';

    /**
     * Path to store config customer update synchronisation
     *
     * @var string
     */
    const XML_PATH_CUSTOMER_UPDATE = 'gravity/sync/customer_update';

    /**
     * Path to store config product update synchronisation
     *
     * @var string
     */
    const XML_PATH_PRODUCT_UPDATE = 'gravity/sync/product_update';

    /**
     * Path to store config customer register synchronisation
     *
     * @var string
     */
    const XML_PATH_CUSTOMER_REGISTER = 'gravity/sync/customer_registration';

    /**
     * Path to store config enabled pages in general layout
     *
     * @var string
     */
    const XML_PATH_ENABLED_PAGES = 'gravity/general_display/pages';

    /**
     * Checks whether extension is enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether extension is in preview mode
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function isPreviewMode($store = null)
    {
        if ($this->isFullyEnabled()) {
            return Mage::getStoreConfigFlag(self::XML_PATH_PREVIEW, $store);
        } else {
            return false;
        }
    }

    /**
     * Checks whether extension is properly enabled, has API user and password
     *
     * @return bool
     */
    public function isFullyEnabled()
    {
        if ($this->isEnabled() && $this->getApiUser() && $this->getApiPassword()) {
            return true;
        }

        return false;
    }

    /**
     * Write log
     *
     * @param string|array|object $log log
     * @return void
     */
    public function getLogger($log)
    {
        Mage::log($log, null, $this->_logfile, true);
    }

    /**
     * Checks whether debug mode is enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getDebugMode($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEBUG, $store);
    }

    /**
     * Get API username
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getApiUser($store = null)
    {
        $apiUser = Mage::getStoreConfig(self::XML_PATH_API_USER, $store);
        if ($apiUser) {
            return $apiUser;
        }

        return false;
    }

    /**
     * Get API password
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getApiPassword($store = null)
    {
        $apiPassword = Mage::getStoreConfig(self::XML_PATH_API_PASSWORD, $store);
        if ($apiPassword) {
            return $apiPassword;
        }

        return false;
    }

    /**
     * Checks whether custom Gravity JS is enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    public function getIsCustomJsEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOM_JS_ENABLED, $store);
    }

    /**
     * Get custom Gravity JS content
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return string
     */
    public function getCustomJs($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOM_JS, $store);
    }

    /**
     * Get catalog export file path
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return string
     */
    public function getExportFilePath($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_EXPORT_PATH, $store);
    }

    /**
     * Get additional catalog attributes for export
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return array|boolean
     */
    public function getAdditionalCatalogAttributes($store = null)
    {
        $additionalAttributes = Mage::getStoreConfig(self::XML_PATH_EXPORT_CATALOG_ADDITIONAL, $store);
        if (!empty($additionalAttributes)) {
            return explode(',', $additionalAttributes);
        } else {
            return false;
        }
    }

    /**
     * Get additional catalog attributes for export
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return array|boolean
     */
    public function getAdditionalCustomerAttributes($store = null)
    {
        $additionalAttributes = Mage::getStoreConfig(self::XML_PATH_EXPORT_CUSTOMER_ADDITIONAL, $store);
        if (!empty($additionalAttributes)) {
            return explode(',', $additionalAttributes);
        } else {
            return false;
        }
    }

    /**
     * Get catalog export all products
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCatalogExportAll($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_CATALOG_EXPORT_ALL_PATH, $store);
    }

    /**
     * Get catalog export max size
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCatalogMaxLimit($store = null)
    {
        return Mage::getStoreConfig(self::XML_CATALOG_EXPORT_MAX_PATH, $store);
    }

    /**
     * Get if product's description attribute enabled in catalog export
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function isDescriptionExportEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_CATALOG_EXPORT_DESCRIPTION, $store);
    }

    /**
     * Get catalog export cron enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCatalogCronEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_CATALOG_EXPORT_CRON, $store);
    }

    /**
     * Get customer export all
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCustomerExportAll($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_CUSTOMER_EXPORT_ALL_PATH, $store);
    }

    /**
     * Get customer export max size
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCustomerMaxLimit($store = null)
    {
        return Mage::getStoreConfig(self::XML_CUSTOMER_EXPORT_MAX_PATH, $store);
    }

    /**
     * Get catalog export only salable products
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getOnlySalable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EXPORT_ONLY_SALABLE, $store);
    }

    /**
     * Get catalog export disabled status products
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getOnlyEnabledStatus($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EXPORT_STATUS, $store);
    }

    /**
     * Get boxes use Gravity template
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function useGravityTemplate($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_TEMPLATE, $store);
    }

    /**
     * Check whether use bulk recommendation
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function useBulkRecommendation($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_BULK, $store);
    }

    /**
     * Get customer update synchronisation enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCustomerUpdateEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_UPDATE, $store);
    }

    /**
     * Get customer registration synchronisation enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getCustomerRegisterEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_REGISTER, $store);
    }


    /**
     * Get product update synchronisation enabled
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return string
     */
    public function getProductUpdateEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_UPDATE, $store);
    }

    /**
     * Get enabled pages in general layout
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    public function getEnabledPages($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED_PAGES, $store);
    }
}
