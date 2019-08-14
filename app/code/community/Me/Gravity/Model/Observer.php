<?php
/**
 * Class Me_Gravity_Block_Abstract
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Abstract
 */
class Me_Gravity_Model_Observer
{
    /**
     * App Gravity block to catalog product view
     *
     * @param Varien_Event_Observer $observer observer
     * @return void|boolean
     */
    public function setGravityProductBlock(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        }

        if ($this->_getGravityHelper()->isPreviewMode()) {
            $previewParam = Mage::app()->getRequest()->getParam('gr_reco_test');
            if (!isset($previewParam) || $previewParam != 'true') {
                return false;
            }
        }

        $controller = $observer->getAction();
        if ($controller->getFullActionName() == 'catalog_product_view') {

            $layout = $controller->getLayout();
            $this->_setProductPageBoxes($layout);

        } elseif ($controller->getFullActionName() == 'catalog_category_view') {

            $layout = $controller->getLayout();
            $this->_setCategoryPageBoxes($layout);

        } elseif ($controller->getFullActionName() == 'catalogsearch_result_index'
            || $controller->getFullActionName() == 'catalogsearch_advanced_result'
        ) {

            $layout = $controller->getLayout();
            $this->_setSearchResultBox($layout);

        } elseif ($controller->getFullActionName() == 'checkout_cart_index') {

            $layout = $controller->getLayout();
            $this->_setCartPageBox($layout);

        } else {
            $enabledPages = explode(',', $this->_getGravityHelper()->getEnabledPages());
            $homePagePath = $this->_getHomePagePath();
            if ($enabledPages
                && ($controller->getFullActionName() == 'cms_index_index' && in_array($homePagePath, $enabledPages))
                || ($controller->getFullActionName() == 'cms_page_view' && in_array($observer->getAction()->getRequest()->getOriginalPathInfo(), $enabledPages))
                || in_array($controller->getFullActionName(), $enabledPages)
            ) {
                $layout = $controller->getLayout();
                $this->_setGeneralBoxes($layout);
            }

        }
    }

    /**
     * Gravity add to cart event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|boolean
     */
    public function gravityAddToCart(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        }

        try {
            $requestParams = Mage::app()->getRequest()->getParams();
            $product = $observer->getProduct();

            if (!is_null($product) && $product->getId() && isset($requestParams['product']) && $requestParams['product']) {

                Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_SEND,
                    array(
                        'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_ADD_TO_CART,
                        'product' => $requestParams['product'],
                        'qty' => isset($requestParams['qty']) ? $requestParams['qty'] : 1,
                        'unitPrice' => $product->getFinalPrice()
                    )
                );

            }
        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending add to cart event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Gravity update cart event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|boolean
     */
    public function gravityUpdateCart(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        }

        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo();

        if ($cart && $data) {

            try {

                foreach ($data as $itemId => $itemInfo) {

                    if ($item = $cart->getQuote()->getItemById($itemId)) {

                        Mage::getModel('me_gravity/method_request')->sendRequest(
                            Me_Gravity_Model_Method_Request::EVENT_TYPE_SEND,
                            array(
                                'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_ADD_TO_CART,
                                'product' => $item->getProductId(),
                                'qty' => $itemInfo['qty'],
                                'unitPrice' => $item->getPrice()
                            )
                        );

                    }

                }

            } catch (Mage_Core_Exception $e) {
                $this->_getGravityHelper()->getLogger($e->getMessage());
            } catch (Exception $e) {
                $this->_getGravityHelper()->getLogger(
                    $e->getMessage(),
                    $this->_getGravityHelper()->__('An error occurred while sending cart update event to Gravity.')
                );
            }
        }

        return $this;
    }

    /**
     * Gravity remove from cart event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|boolean
     */
    public function gravityRemoveFromCart(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        }

        try {
            $product = $observer->getQuoteItem()->getProduct();
            $qty = $observer->getQuoteItem()->getQty();

            if (!is_null($product) && $product->getId()) {

                Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_SEND,
                    array(
                        'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_REMOVE_FROM_CART,
                        'product' => $product->getId(),
                        'qty' => isset($qty) ? $qty : 1
                    )
                );

            }

        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending remove form cart event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Gravity buy event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|boolean
     */
    public function gravitySendBuy(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled()) {
            return false;
        }

        try {

            $order = $observer->getOrder();
            if (!is_null($order) && $order->getId()) {

                $orderItem = array();
                $orderId = $order->getEntityId();
                $baseCurrency = $order->getBaseCurrencyCode();
                foreach ($order->getAllItems() as $item) {

                    Mage::getModel('me_gravity/method_request')->sendRequest(
                        Me_Gravity_Model_Method_Request::EVENT_TYPE_SEND,
                        array(
                            'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_BUY,
                            'orderId' => $orderId,
                            'itemId' => $item->getProduct()->getId(),
                            'unitPrice' => $item->getBasePrice(),
                            'quantity' => $item->getQtyOrdered(),
                            'currency' => $baseCurrency
                        )
                    );
                }

            }

        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending buy event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Gravity customer update event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|bool
     */
    public function gravityUserUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled() || !$this->_getGravityHelper()->getCustomerUpdateEnabled()) {
            return false;
        }

        try {

            $customer = $observer->getCustomer();
            if (!is_null($customer) && $customer->getId()) {

                $parameters = array(
                    'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_CUSTOMER_UPDATE
                );

                $exportModel = Mage::getModel('me_gravity/customers');
                foreach ($exportModel->getExportHeaders() as $attribute) {
                    if ($attribute == 'userid') {
                        $parameters[$attribute] = $customer->getId();
                    } elseif ($attributeValue = $exportModel->getAttributeValueByCode($customer, $attribute)) {
                        $parameters[$attribute] = $attributeValue;
                    }
                }

                Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_UPDATE,
                    $parameters
                );

            }

        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending customer update event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Gravity customer registration event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|bool
     */
    public function gravityCustomerRegistration(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled() || !$this->_getGravityHelper()->getCustomerRegisterEnabled()) {
            return false;
        }

        try {

            $customer = $observer->getCustomer();
            if (!is_null($customer) && $customer->getId()) {

                $parameters = array(
                    'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_CUSTOMER_UPDATE,
                    'userid' => $customer->getId(),
                    'email' => $customer->getEmail(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname()
                );

                Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_UPDATE,
                    $parameters
                );

            }

        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending customer register event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Gravity product update event observer
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this
     */
    public function gravityProductUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getGravityHelper()->isFullyEnabled() || !$this->_getGravityHelper()->getProductUpdateEnabled()) {
            return false;
        }

        try {
            $storeId = Mage::app()->getDefaultStoreView()->getId();
            $param = Mage::app()->getRequest()->getParam('store');
            if (isset($param) && $param) {
                $storeId = $param;
            }
            $product = $observer->getProduct();
            if (!is_null($product) && $product->getId()) {

                $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                $exportModel = Mage::getModel('me_gravity/products');
                $productUrl = htmlspecialchars($baseUrl . $product->getUrlPath());
                $parameters = array(
                    'type' => Me_Gravity_Model_Method_Request::EVENT_TYPE_PRODUCT_UPDATE,
                    'itemid' => $product->getId(),
                    'title' => htmlspecialchars($product->getName()),
                    'hidden' => ($product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) ? 'true' : 'false',
                    'link' => $productUrl ? $productUrl : $product->getProductUrl(),
                    'image_link' => htmlspecialchars($this->_getCatalogBaseMediaUrl() . $product->getImage()),
                    'description' => htmlspecialchars($product->getDescription()),
                    'price' => $product->getFinalPrice(),
                    'categoryPath' => implode(',', $exportModel->getCategoryPath($product->getCategoryIds(), $storeId)),
                    'categoryId' => implode(',', $product->getCategoryIds()),
                    'storeId' => $storeId
                );

                $additionalParameters = $exportModel->getAdditionalAttributesXml($product, false, $storeId);
                if ($additionalParameters) {
                    $parameters = array_merge($parameters, $additionalParameters);
                }

                Mage::getModel('me_gravity/method_request')->sendRequest(
                    Me_Gravity_Model_Method_Request::EVENT_TYPE_UPDATE,
                    $parameters
                );

            }

        } catch (Mage_Core_Exception $e) {
            $this->_getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->_getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->_getGravityHelper()->__('An error occurred while sending product update event to Gravity.')
            );
        }

        return $this;
    }

    /**
     * Set general page boxes
     *
     * @param Mage_Core_Model_Layout $layout layout
     * @return void
     */
    protected function _setGeneralBoxes($layout)
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $boxNames = array(
            'best',
            'history',
            'others',
            'popular'
        );

        foreach ($boxNames as $name) {

            if ($boxHelper->getBoxEnabled($name, 'general')) {

                $block = $layout->createBlock('me_gravity/general_boxes_' . $name)
                    ->setName('me.gravity.general.' . $name);

                if (Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT == $boxHelper->getBoxLayout($name, 'general')) {
                    $block->setTemplate('me/gravity/general/boxes/default.phtml');
                    if ($reference = $layout->getBlock(Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT)) {
                        $reference->append($block);
                    }
                } else {
                    $block->setTemplate('me/gravity/general/boxes/sidebar.phtml');
                    if ($reference = $layout->getBlock($boxHelper->getBoxLayout($name, 'general'))) {
                        $reference->append($block);
                    }
                }
            }

        }
    }

    /**
     * Set category page boxes
     *
     * @param Mage_Core_Model_Layout $layout layout
     * @return void
     */
    protected function _setCategoryPageBoxes($layout)
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $boxNames = array(
            'personal',
            'top'
        );

        foreach ($boxNames as $name) {

            if ($boxHelper->getBoxEnabled($name, 'category')) {

                $block = $layout->createBlock('me_gravity/catalog_category_boxes_' . $name)
                    ->setName('me.gravity.category.' . $name);

                if (Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT == $boxHelper->getBoxLayout($name, 'category')) {
                    $block->setTemplate('me/gravity/catalog/category/boxes/default.phtml');
                    if ($reference = $layout->getBlock(Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT)) {
                        $reference->append($block);
                    }
                } else {
                    $block->setTemplate('me/gravity/catalog/category/boxes/sidebar.phtml');
                    if ($reference = $layout->getBlock($boxHelper->getBoxLayout($name, 'category'))) {
                        $reference->append($block);
                    }
                }
            }

        }
    }

    /**
     * Set product page boxes
     *
     * @param Mage_Core_Model_Layout $layout layout
     * @return void
     */
    protected function _setProductPageBoxes($layout)
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $boxNames = array(
            'similar',
            'personal',
            'accessories'
        );

        foreach ($boxNames as $name) {

            if ($boxHelper->getBoxEnabled($name, 'product')) {
                $block = $layout->createBlock('me_gravity/catalog_product_view_boxes_' . $name)
                    ->setName('me.gravity.product.view.' . $name);
                if (Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT == $boxHelper->getBoxLayout($name, 'product')) {
                    $block->setTemplate('me/gravity/catalog/product/view/boxes/default.phtml');
                    if ($reference = $layout->getBlock(Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT)) {
                        $reference->append($block);
                    }
                } else {
                    $block->setTemplate('me/gravity/catalog/product/view/boxes/sidebar.phtml');
                    if ($reference = $layout->getBlock($boxHelper->getBoxLayout($name, 'product'))) {
                        $reference->append($block);
                    }
                }
            }

        }
    }

    /**
     * Set search result page box
     *
     * @param Mage_Core_Model_Layout $layout layout
     * @return void
     */
    protected function _setSearchResultBox($layout)
    {
        $boxHelper = $this->_getGravityBoxHelper();

        if ($boxHelper->getBoxEnabled('search', 'search')) {

            $block = $layout->createBlock('me_gravity/catalogsearch_boxes_result')
                ->setName('me.gravity.catalogsearch.result');

            if (Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT == $boxHelper->getBoxLayout('search', 'search')) {
                $block->setTemplate('me/gravity/catalogsearch/boxes/default.phtml');
                if ($reference = $layout->getBlock(Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT)) {
                    $reference->append($block);
                }
            } else {
                $block->setTemplate('me/gravity/catalogsearch/boxes/sidebar.phtml');
                if ($reference = $layout->getBlock($boxHelper->getBoxLayout('search', 'search'))) {
                    $reference->append($block);
                }
            }
        }
    }

    /**
     * Set cart page box
     *
     * @param Mage_Core_Model_Layout $layout layout
     * @return void
     */
    protected function _setCartPageBox($layout)
    {
        $boxHelper = $this->_getGravityBoxHelper();

        if ($boxHelper->getBoxEnabled('cart', 'cart')) {

            $block = $layout->createBlock('me_gravity/checkout_cart_boxes_cart')
                ->setName('me.gravity.checkout.cart');

            if (Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT == $boxHelper->getBoxLayout('cart', 'cart')) {
                $block->setTemplate('me/gravity/checkout/cart/boxes/default.phtml');
                if ($reference = $layout->getBlock(Me_Gravity_Model_System_Config_Source_Layout_Layout::LAYOUT_CONTENT)) {
                    $reference->append($block);
                }
            } else {
                $block->setTemplate('me/gravity/checkout/cart/boxes/boxes/sidebar.phtml');
                if ($reference = $layout->getBlock($boxHelper->getBoxLayout('cart', 'cart'))) {
                    $reference->append($block);
                }
            }
        }
    }

    /**
     * Schedule catalog export
     *
     * @return void|boolean
     */
    public function scheduledExport()
    {
        $gravityHelper = $this->_getGravityHelper();

        try {

            $currentStore = Mage::app()->getStore()->getCode();
            $gravityHelper->getLogger('$currentStore: ' . $currentStore);
            Mage::app()->setCurrentStore('admin');

            foreach (Mage::app()->getStores() as $store) {

                if (!$gravityHelper->isEnabled($store->getId())) {
                    continue;
                }

                $filename = Mage::getModel('me_gravity/products')->generateCatalogXml($store->getId());
                if ($filename) {
                    $gravityHelper->getLogger($gravityHelper->__('Store Id: %s exported to %s', $store->getId(), $filename));
                } else {
                    $gravityHelper->getLogger($gravityHelper->__('Store Id: %s exported error', $store->getId()));
                }
            }

            Mage::app()->setCurrentStore($currentStore);

        } catch (Mage_Core_Exception $e) {
            $gravityHelper->getLogger($e->getMessage());
        } catch (Exception $e) {
            $gravityHelper->getLogger(
                $e->getMessage(),
                $gravityHelper->__('An error occurred while exporting products')
            );
        }
    }

    /**
     * Retrieve Gravity boxes helper
     *
     * @return Me_Gravity_Helper_Boxes
     */
    protected function _getGravityBoxHelper()
    {
        return Mage::helper('me_gravity/boxes');
    }

    /**
     * Retrieve Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    protected function _getGravityHelper()
    {
        return Mage::helper('me_gravity');
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
     * Get store home page
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return string
     */
    protected function _getHomePagePath($store = null)
    {
        return Mage::getStoreConfig('web/default/cms_home_page', $store);
    }
}