<?php
/**
 * Class Me_Gravity_Model_Products
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Products
 */
class Me_Gravity_Model_Products extends Me_Gravity_Model_Export_Catalog_Product
{
    /**
     * Path to store config catalog export file save path
     *
     * @var string
     */
    const XML_CATALOG_EXPORT_PATH = 'gravity/export/catalog_path';

    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * Items array
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Generate product catalog xml
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function generateCatalogXml()
    {
        $this->_validatePath();
        $baseUrl = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->_getCatalogExportPath()));

        if ($io->fileExists($this->_getGravityCatalogFilename()) && !$io->isWriteable($this->_getGravityCatalogFilename())) {
            Mage::throwException(
                Mage::helper('me_gravity')->__(
                    'File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.',
                    $this->_getGravityCatalogFilename(),
                    $this->_getCatalogExportPath()
                )
            );
        }

        $io->streamOpen($this->_getGravityCatalogFilename());

        $io->streamWrite('<?xml version="1.0"?>' . "\n");
        $io->streamWrite('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">');
        $io->streamWrite('<channel>');
        $io->streamWrite('<title>' . Mage::app()->getStore()->getFrontendName() . ' Gravity Reco item catalog</title>');
        $io->streamWrite('<link>' . $baseUrl . '</link>');
        $io->streamWrite('<description>' . Mage::app()->getStore()->getFrontendName() . ' item catalog description</description>');

        $stores = Mage::app()->getStores();
        ksort($stores);

        $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        $defaultCurrency = Mage::app()->getDefaultStoreView()->getBaseCurrencyCode();
        $directoryHelper = Mage::helper('directory');
        $isDescriptionEnabled = $this->_helper->isDescriptionExportEnabled();

        foreach ($stores as $store) {

            $productCollection = $this->getProductCollection($store->getId());

            if ($productCollection->count()) {

                $currentCurrencyCode = $store->getCurrentCurrencyCode();
                foreach ($productCollection as $product) {

                    if ($store->getId() == $defaultStoreId) {
                        $this->_items[$product->getId()]['default_title'] = htmlspecialchars($product->getName());
                    }

                    $this->_items[$product->getId()][$store->getId()]['title'] = htmlspecialchars($product->getName());
                    $this->_items[$product->getId()][$store->getId()]['hidden'] = $product->getVisibility();
                    $this->_items[$product->getId()][$store->getId()]['status'] = ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) ? 'true' : 'false';
                    $this->_items[$product->getId()][$store->getId()]['link'] = htmlspecialchars($baseUrl . $product->getUrlPath());
                    $this->_items[$product->getId()][$store->getId()]['image_link'] = htmlspecialchars($this->_getCatalogBaseMediaUrl() . $product->getImage());
                    if ($isDescriptionEnabled) {
                        $this->_items[$product->getId()][$store->getId()]['description'] = htmlspecialchars($product->getDescription(), ENT_COMPAT | ENT_DISALLOWED, 'UTF-8', true);
                    }
                    if ($store->getId() == $defaultStoreId) {
                        $this->_items[$product->getId()][$store->getId()]['price'] = $product->getFinalPrice();
                    } else {
                        $this->_items[$product->getId()][$store->getId()]['price'] = $directoryHelper->currencyConvert($product->getFinalPrice(), $defaultCurrency, $currentCurrencyCode);
                    }
                    $this->_items[$product->getId()][$store->getId()]['categoryPath'] = $this->getCategoryPath($product->getCategoryIds(), $store->getId());
                    $this->_items[$product->getId()][$store->getId()]['categoryId'] = $product->getCategoryIds();
                    $this->_items[$product->getId()][$store->getId()]['attributes'] = $this->getAdditionalAttributesXml($product, false, $store->getId());
                    $this->_items[$product->getId()][$store->getId()]['currency'] = $currentCurrencyCode;
                }

            }

        }

        if (!empty($this->_items)) {

            foreach ($this->_items as $productId => $storeItem) {

                $itemRow = $this->_createItemXml($storeItem, $productId, $isDescriptionEnabled);
                if ($itemRow) {
                    $io->streamWrite($itemRow);
                }

            }

        }

        $io->streamWrite('</channel>');
        $io->streamWrite('</rss>');
        $io->streamClose();

        return $this->_getGravityCatalogFilename();
    }

    /**
     * Get item xml
     *
     * @param array $storeItems           items
     * @param int   $productId            product Id
     * @param bool  $isDescriptionEnabled description enabled
     * @return string|bool
     */
    protected function _createItemXml($storeItems = array(), $productId = 0, $isDescriptionEnabled = false)
    {
        $itemXml = '';
        $defaultTitle = '';

        if (isset($storeItems['default_title'])) {
            $defaultTitle = $storeItems['default_title'];
            unset($storeItems['default_title']);
        } else {
            $this->_helper->getLogger('Invalid default title. Product Id: ' . $productId);
            return false;
        }

        // add fix elements
        $itemXml = sprintf(
            '<item>%s',
            $this->_getFixElements($productId, $defaultTitle)
        );

        // add store elements
        foreach ($storeItems as $storeId => $item) {

            if ($isDescriptionEnabled) {
                $itemXml .= sprintf(
                    '<title_' . $storeId . '><![CDATA[%s]]></title_' . $storeId . '><currency_' . $storeId . '>%s</currency_' . $storeId . '>%s<status_' . $storeId . '>%s</status_' . $storeId . '><link_' . $storeId . '>%s</link_' . $storeId . '><g:image_link_' . $storeId . '>%s</g:image_link_' . $storeId . '><c:description_' . $storeId . '><![CDATA[%s]]></c:description_' . $storeId . '><g:price_' . $storeId . '>%.4f</g:price_' . $storeId . '>%s%s%s',
                    $item['title'],
                    $item['currency'],
                    $this->_getVisibilityElements($item['hidden'], $storeId),
                    $item['status'],
                    $item['link'],
                    $item['image_link'],
                    $item['description'],
                    $item['price'],
                    $this->_getCategoryXml($item, $storeId),
                    $this->_getCategoryIdsXml($item, $storeId),
                    $this->_getAttributesXml($item, $storeId)
                );
            } else {
                $itemXml .= sprintf(
                    '<title_' . $storeId . '><![CDATA[%s]]></title_' . $storeId . '><currency_' . $storeId . '>%s</currency_' . $storeId . '>%s<status_' . $storeId . '>%s</status_' . $storeId . '><link_' . $storeId . '>%s</link_' . $storeId . '><g:image_link_' . $storeId . '>%s</g:image_link_' . $storeId . '><g:price_' . $storeId . '>%.4f</g:price_' . $storeId . '>%s%s%s',
                    $item['title'],
                    $item['currency'],
                    $this->_getVisibilityElements($item['hidden'], $storeId),
                    $item['status'],
                    $item['link'],
                    $item['image_link'],
                    $item['price'],
                    $this->_getCategoryXml($item, $storeId),
                    $this->_getCategoryIdsXml($item, $storeId),
                    $this->_getAttributesXml($item, $storeId)
                );
            }

        }

        $itemXml .= '</item>';

        return $itemXml;
    }

    /**
     * Get first two fix xml element
     *
     * @param int    $productId product Id
     * @param string $title     title
     * @return string
     */
    protected function _getFixElements($productId, $title)
    {
        $fixXml = '';

        $fixXml .= sprintf(
            '<g:id>%s</g:id><title><![CDATA[%s]]></title>',
            $productId,
            $title
        );

        return $fixXml;
    }

    /**
     * Get hidden xml element
     *
     * @param int $visibility visibility
     * @param int $storeId    store Id
     * @return string
     */
    protected function _getVisibilityElements($visibility = 0, $storeId = 0)
    {
        $visibilityXml = '';

        switch ($visibility) {
            case Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE:
                $visibilityXml = '<c:hidden_' . $storeId . '>hidden</c:hidden_' . $storeId . '>';
                break;
            case Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG:
                $visibilityXml = '<c:hidden_' . $storeId . '>catalog</c:hidden_' . $storeId . '>';
                break;
            case Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH:
                $visibilityXml = '<c:hidden_' . $storeId . '>search</c:hidden_' . $storeId . '>';
                break;
            case Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH:
                $visibilityXml = '<c:hidden_' . $storeId . '>catalog</c:hidden_' . $storeId . '><c:hidden_' . $storeId . '>search</c:hidden_' . $storeId . '>';
                break;
        }

        return $visibilityXml;
    }

    /**
     * Get category information XML part
     *
     * @param array $item    item
     * @param int   $storeId store Id
     * @return string
     */
    protected function _getCategoryXml($item = array(), $storeId = 0)
    {
        $categoryXml = '';

        if (isset($item['categoryPath']) && is_array($item['categoryPath'])) {
            foreach ($item['categoryPath'] as $categoryPath)
                $categoryXml .= sprintf(
                    '<c:categoryPath_' . $storeId . '><![CDATA[%s]]></c:categoryPath_' . $storeId . '>',
                    $categoryPath
                );
        }

        return $categoryXml;
    }

    /**
     * Get category isd information XML part
     *
     * @param array $item    item
     * @param int   $storeId store Id
     * @return string
     */
    protected function _getCategoryIdsXml($item = array(), $storeId = 0)
    {
        $categoryIdsXml = '';

        if (isset($item['categoryId']) && is_array($item['categoryId'])) {
            foreach ($item['categoryId'] as $categoryId)
                $categoryIdsXml .= sprintf(
                    '<c:categoryId_' . $storeId . '>%s</c:categoryId_' . $storeId . '>',
                    $categoryId
                );
        }

        return $categoryIdsXml;
    }

    /**
     * Get attributes xml for each store
     *
     * @param array $item    item
     * @param int   $storeId store Id
     * @return string
     */
    protected function _getAttributesXml($item = array(), $storeId = 0)
    {
        $attributesXml = '';

        if (isset($item['attributes']) && is_array($item['attributes'])) {
            foreach ($item['attributes'] as $attributeCode => $attribute)
                $attributesXml .= sprintf(
                    '<' . $attributeCode . '><![CDATA[%s]]></' . $attributeCode . '>',
                    $attribute
                );
        }

        return $attributesXml;
    }

    /**
     * Get category path by category ids
     *
     * @param array $categoryIds category ids
     * @param int   $storeId     store id
     * @return string
     */
    public function getCategoryPath($categoryIds, $storeId = 0)
    {
        $categoryPath = array();

        if ($categoryIds) {

            foreach ($categoryIds as $categoryId) {
                if (array_key_exists($categoryId, $this->_categories[$storeId])) {
                    $categoryPath[] = htmlspecialchars($this->_categories[$storeId][$categoryId]);
                }
            }

        }

        return $categoryPath;
    }

    /**
     * Get additional product attributes XML
     *
     * @param Mage_Catalog_Model_Product $product product
     * @param boolean                    $asXml   as xml
     * @param int                        $storeId store id
     * @return string|array
     */
    public function getAdditionalAttributesXml(Mage_Catalog_Model_Product $product, $asXml = true, $storeId = 0)
    {
        $additionalXml = '';

        if (!empty($this->_attributeValues)) {

            foreach ($this->_attributeValues as $attributeCode => $attributeValues) {

                $additionalValues = array();
                $productValue = $product->getData($attributeCode);
                if ($productValue) {

                    if (is_array($productValue)) {
                        foreach ($productValue as $optionId) {
                            if (array_key_exists($optionId, $this->_attributeValues[$attributeCode][$storeId])) {
                                $additionalValues[] = $this->_attributeValues[$attributeCode][$storeId][$optionId];
                            }
                        }
                    } else {
                        if (!empty($this->_attributeValues[$attributeCode]) && array_key_exists($productValue, $this->_attributeValues[$attributeCode][$storeId])) {
                            $additionalValues[] = $this->_attributeValues[$attributeCode][$storeId][$productValue];
                        } else {

                            $additionalValues[] = $productValue;

                        }
                    }

                    if (!empty($additionalValues)) {

                        if ($asXml) {
                            $additionalXml .= sprintf(
                                '<' . $attributeCode . '_' . $storeId . '><![CDATA[%s]]></' . $attributeCode . '>',
                                htmlspecialchars(implode(',', $additionalValues))
                            );
                        } else {
                            $additionalXml[$attributeCode . '_' . $storeId] = htmlspecialchars(implode(',', $additionalValues));
                        }

                    }

                }

            }

        }

        return $additionalXml;
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function _getCatalogExportPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() . DS . $this->_getCatalogFilePath());
        }
        return $this->_filePath;
    }

    /**
     * Validate catalog export file save path
     *
     * @throws Mage_Core_Exception
     * @return void
     */
    protected function _validatePath()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->_getCatalogFilePath());

        /**
         * Check path is allow
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException($this->_helper->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(
                $this->_helper->__(
                    'Please create the specified folder "%s" before saving the sitemap.',
                    Mage::helper('core')->escapeHtml($this->_getCatalogFilePath())
                )
            );
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException($this->_helper->__('Please make sure that "%s" is writable by web-server.', $this->_getCatalogFilePath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->_getGravityCatalogFilename())) {
            Mage::throwException(
                $this->_helper->__(
                    'Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'
                )
            );
        }
    }

    /**
     * Get catalog export filename
     *
     * @return string
     */
    protected function _getGravityCatalogFilename()
    {
        return 'gravity.xml';
    }

    /**
     * Web-based directory path of product images
     *
     * @return string
     */
    protected function _getCatalogBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrlAddition();
    }

    /**
     * Get catalog export file path
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return bool
     */
    private function _getCatalogFilePath($store = null)
    {
        return Mage::getStoreConfig(self::XML_CATALOG_EXPORT_PATH, $store);
    }
}
