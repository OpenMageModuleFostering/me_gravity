<?php
/**
 * Class Me_Gravity_Model_Export_Catalog_Product
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Export_Catalog_Product
 */
class Me_Gravity_Model_Export_Catalog_Product extends Me_Gravity_Model_Export_Abstract
{
    /**
     * Maximum items to export
     *
     * @var int
     */
    const MAX_LIMIT = 1000;

    /**
     * Base attributes to export
     *
     * @var array
     */
    protected $_baseAttributeCodes = array(
        'status',
        'visibility',
        'name',
        'description',
        'image',
        'url_path',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date'
    );

    /**
     * Disabled attribute types
     *
     * @var array
     */
    protected $_excludedAttributesTypes = array(
        'price',
        'date',
        'media_image'
    );

    /**
     * Disabled attribute codes
     *
     * @var array
     */
    protected $_excludedAttributeCodes = array(
        'custom_design',
        'custom_layout_update',
        'gallery',
        'media_gallery',
        'msrp_display_actual_price_type',
        'msrp_enabled',
        'options_container',
        'price_view',
        'tier_price'
    );

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array(
        'status',
        'tax_class_id',
        'visibility',
        'gift_message_available',
        'custom_design'
    );

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection;

    /**
     * Categories ID to text-path hash.
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Root category names for each category
     *
     * @var array
     */
    protected $_rootCategories = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initCategories();
        $this->_initAttributes();
    }

    /**
     * Export product collection
     *
     * @param int $storeId store id
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection($storeId = 0)
    {
        $productCollection = $this->_addBaseAttributes($this->_getProductCollection())
            ->setStoreId($storeId)
            ->addWebsiteFilter(Mage::app()->getStore($storeId)->getWebsiteId())
            ->addCategoryIds();

        if ($this->_helper->getOnlySalable()) {
            $productCollection->addFinalPrice();
        }

        if (!$this->_helper->getCatalogExportAll()) {
            if ($this->_helper->getCatalogMaxLimit()) {
                $productCollection->setPageSize((int)$this->_helper->getCatalogMaxLimit());
            } else {
                $productCollection->setPageSize(self::MAX_LIMIT);
            }
        }

        if (!$this->_helper->getOnlyEnabledStatus()) {
            $productCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        }
        $productCollection->load();

        return $productCollection;
    }

    /**
     * Add base attributes to collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _addBaseAttributes(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {
        $exportAttrCodes = $this->getExportAttrCodes();

        if (!empty($this->_attributeValues)) {
            $exportAttrCodes = array_merge($exportAttrCodes, array_keys($this->_attributeValues));
        }

        foreach ($this->getAttributeCollection() as $attribute) {

            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, $exportAttrCodes) && !in_array($attributeCode, $this->_excludedAttributeCodes)) {
                $collection->addAttributeToSelect($attributeCode);
            }

        }

        return $collection;
    }

    /**
     * Get base product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection()
    {
        return Mage::getResourceModel('catalog/product_collection');
    }

    /**
     * Initialize categories ID to text-path hash.
     *
     * @return Me_Gravity_Model_Export_Catalog_Product
     */
    protected function _initCategories()
    {
        $stores = Mage::app()->getStores();

        foreach ($stores as $store) {
            $collection = Mage::getResourceModel('catalog/category_collection')
                ->setStoreId($store->getId())
                ->addNameToResult();

            foreach ($collection as $category) {
                $structure = preg_split('#/+#', $category->getPath());
                $pathSize = count($structure);
                if ($pathSize > 1) {
                    $path = array();
                    for ($i = 1; $i < $pathSize; $i++) {
                        $path[] = $collection->getItemById($structure[$i])->getName();
                    }
                    $this->_rootCategories[$store->getId()][$category->getId()] = array_shift($path);
                    if ($pathSize > 2) {
                        $this->_categories[$store->getId()][$category->getId()] = implode('/', $path);
                    }
                }

            }
        }

        return $this;
    }

    /**
     * Initialize attribute option values and types
     *
     * @return $this
     */
    protected function _initAttributes()
    {
        if ($additionalAttributes = $this->_helper->getAdditionalCatalogAttributes()) {
            foreach (Mage::getResourceModel('catalog/product_attribute_collection') as $attribute) {
                if (in_array($attribute->getAttributeCode(), $additionalAttributes)) {
                    $this->_attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
                }
            }
        }

        return $this;
    }
}
