<?php
/**
 * Class Me_Salesautopilot_Model_Method_Request
 *
 * @category  Me
 * @package   Me_Salesautopilot
 * @author    Sági Attila <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Method_Request
 */
require_once 'app/code/community/Me/Gravity/Model/Client/GravityClient.php';

class Me_Gravity_Model_Method_Request extends Me_Gravity_Model_Method_Abstract
{
    /**
     * Request event type
     */
    const EVENT_TYPE_GET = 'GET';

    /**
     * Send event type
     */
    const EVENT_TYPE_SEND = 'SEND';

    /**
     * Test event type
     */
    const EVENT_TYPE_TEST = 'TEST';

    /**
     * Update / add event type
     */
    const EVENT_TYPE_UPDATE = 'UPDATE';

    /**
     * Event type customer update
     */
    const EVENT_TYPE_CUSTOMER_UPDATE = 'CUSTOMER_UPDATE';

    /**
     * Event type product update
     */
    const EVENT_TYPE_PRODUCT_UPDATE = 'PRODUCT_UPDATE';

    /**
     * Event type add to cart
     */
    const EVENT_TYPE_ADD_TO_CART = 'ADD_TO_CART';

    /**
     * Event type remove from cart
     */
    const EVENT_TYPE_REMOVE_FROM_CART = 'REMOVE_FROM_CART';

    /**
     * Event type buy
     */
    const EVENT_TYPE_BUY = 'BUY';

    /**
     * Product page similar
     */
    const PRODUCT_PAGE_SIMILAR = 'ITEM_PAGE_SIMILAR';

    /**
     * Product page personal
     */
    const PRODUCT_PAGE_PERSONAL = 'ITEM_PAGE_PERSONAL';

    /**
     * Product page accessories
     */
    const PRODUCT_PAGE_ACCESSORIES = 'ITEM_PAGE_ACCESSORIES';

    /**
     * Category page personal
     */
    const CATEGORY_PAGE_PERSONAL = 'CATEGORY_PAGE_PERSONAL';

    /**
     * Category page top
     */
    const CATEGORY_PAGE_TOP = 'CATEGORY_PAGE_POP';

    /**
     * Cart page
     */
    const CART_PAGE = 'CART_PAGE';

    /**
     * Search result page
     */
    const SEARCH_RESULT_PAGE = 'LISTING_PAGE';

    /**
     * General page, Personal Best Box
     */
    const GENERAL_PERSONAL_BEST = 'PERSONAL_BEST';

    /**
     * General page, History Box
     */
    const GENERAL_PERSONAL_HISTORY = 'PERSONAL_HISTORY';

    /**
     * General page, Currently Viewed Box
     */
    const GENERAL_CURRENTLY_VIEWED = 'CURRENTLY_VIEWED';

    /**
     * General page, Popular Box
     */
    const GENERAL_POPULAR = 'POPULAR';

    /**
     * Gravity cookie name
     */
    const GRAVITY_COOKIE_NAME = 'gr_reco';

    /**
     * Path to store config API password
     *
     * @var string
     */
    const XML_PATH_API_URL = 'gravity/config/api_url';

    /**
     * Communication method features
     *
     * @var bool
     */
    protected $_canSendRequest = true;

    /**
     * Gravity unique cookie id
     *
     * @var string
     */
    protected $_gravityCookie = '';

    /**
     * Current store id
     *
     * @var int
     */
    protected $_storeId = 0;

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->_helper = $this->_getGravityHelper();
        $this->_storeId = Mage::app()->getStore()->getId();
        if ($this->_helper->getDebugMode()) {
            $this->_canDebugLog = true;
        }
    }

    /**
     * Send request
     *
     * @param string $type   event type
     * @param array  $params parameters
     * @throws Exception
     * @return $this|bool
     */
    public function sendRequest($type, $params = array())
    {
        parent::sendRequest($type, $params);

        if (!$this->_helper->isFullyEnabled()) {
            $this->_helper->getLogger($this->_helper->__('Invalid Gravity extension configuration.'));
            return false;
        }

        try {

            $client = $this->_init();
            $this->_getGravityCookie();

            if ($this->_canDebugLog) {
                $this->_helper->getLogger($type);
                if (isset($params['type']) && $params['type']) {
                    $this->_helper->getLogger($params['type']);
                }
            }

            switch ($type) {
                case self::EVENT_TYPE_GET:
                    $result = $this->_getRecommendationItems($client, $params);
                    break;
                case self::EVENT_TYPE_TEST:
                    $result = $client->test($this->_helper->getApiUser());
                    break;
                case self::EVENT_TYPE_SEND:
                    $result = $this->_sendEvent($client, $params);
                    break;
                case self::EVENT_TYPE_UPDATE:
                    $result = $this->_sendUpdate($client, $params);
                    break;
                default:
                    return false;
            }

            if ($this->_canDebugLog && $result) {
                $this->_helper->getLogger($result);
            }

            return $this->_validateResult($result);

        } catch (Mage_Core_Exception $e) {

            $this->_helper->getLogger($e->getMessage());

        } catch (Exception $e) {

            $this->_helper->getLogger($e->getMessage());

        }

        return $this;
    }

    /**
     * Send event
     *
     * @param GravityClient $client client
     * @param array         $params parameters
     * @throws Mage_Core_Exception
     * @return object|array
     */
    protected function _sendEvent($client, $params)
    {
        if (!isset($params['type'])) {
            Mage::throwException($this->_helper->__('Invalid recommendation type'));
        }

        $event = new GravityEvent();
        if (isset($params['type']) && $params['type']) {
            $event->eventType = $params['type'];
        } else {
            Mage::throwException($this->_helper->__('Invalid event type'));
        }

        $event->userId = $this->_getCustomerId();
        $event->cookieId = $this->_getGravityCookie();
        $event->time = time();

        $storeId = new GravityNameValue('storeId', $this->_storeId);

        switch ($params['type']) {
            case self::EVENT_TYPE_ADD_TO_CART:
                if (isset($params['product'])
                    && isset($params['qty'])
                    && $params['product']
                    && $params['qty']
                ) {
                    $event->itemId = $params['product'];
                    $qty = new GravityNameValue('quantity', $params['qty']);
                    $price = new GravityNameValue('unitPrice', $params['unitPrice']);
                    $event->nameValues = array($qty, $price, $storeId);
                    if ($this->_canDebugLog) {
                        $this->_helper->getLogger('Item id: ' . $event->itemId);
                        $this->_helper->getLogger('Qty: ' . $params['qty']);
                        $this->_helper->getLogger('unitPrice: ' . $params['unitPrice']);
                        $this->_helper->getLogger('StoreId: ' . $this->_storeId);
                    }
                } else {
                    Mage::throwException($this->_helper->__('Invalid ' . $params['type'] . ' parameters'));
                }
                break;
            case self::EVENT_TYPE_REMOVE_FROM_CART:
                if (isset($params['product']) && isset($params['qty']) && $params['product'] && $params['qty']) {
                    $event->itemId = $params['product'];
                    $qty = new GravityNameValue('quantity', $params['qty']);
                    $event->nameValues = array($qty, $storeId);
                    if ($this->_canDebugLog) {
                        $this->_helper->getLogger('Item id: ' . $event->itemId);
                        $this->_helper->getLogger('Qty: ' . $params['qty']);
                        $this->_helper->getLogger('StoreId: ' . $this->_storeId);
                    }
                } else {
                    Mage::throwException($this->_helper->__('Invalid ' . $params['type'] . ' parameters'));
                }
                break;
            case self::EVENT_TYPE_BUY:
                if (isset($params['orderId'])
                    && isset($params['itemId'])
                    && isset($params['unitPrice'])
                    && isset($params['quantity'])
                    && $params['orderId']
                    && $params['itemId']
                    && $params['unitPrice']
                    && $params['quantity']
                ) {
                    $event->itemId = $params['itemId'];
                    $quantity = new GravityNameValue('quantity', $params['quantity']);
                    $unitPrice = new GravityNameValue('unitPrice', $params['unitPrice']);
                    $currency = new GravityNameValue('currency', $params['currency']);
                    $orderId = new GravityNameValue('orderId', $params['orderId']);
                    $event->nameValues = array($quantity, $unitPrice, $currency, $orderId, $storeId);
                    if ($this->_canDebugLog) {
                        $this->_helper->getLogger('Item id: ' . $event->itemId);
                        $this->_helper->getLogger('Qty: ' . $params['quantity']);
                        $this->_helper->getLogger('UnitPrice: ' . $params['unitPrice']);
                        $this->_helper->getLogger('Currency: ' . $params['currency']);
                        $this->_helper->getLogger('OrderId: ' . $params['orderId']);
                        $this->_helper->getLogger('StoreId: ' . $this->_storeId);
                    }
                } else {
                    Mage::throwException($this->_helper->__('Invalid ' . $params['type'] . ' parameters'));
                }
                break;
            default:
                return false;
        }

        $eventsToAdd = array($event);
        $async = false;
        try {

            $result = $client->addEvents($eventsToAdd, $async);

            return $result;

        } catch (GravityException $e) {
            $this->_helper->getLogger($this->_helper->__('Error happened by sending the event!'));
            $this->_helper->getLogger($this->_helper->__('Message: ' . $e->getMessage() . ' Fault info: ' . $e->faultInfo));
        }
    }

    /**
     * Get recommendation
     *
     * @param GravityClient $client client
     * @param array         $params parameters
     * @throws Mage_Core_Exception
     * @return object
     */
    protected function _getRecommendationItems($client, $params)
    {
        if (!isset($params['type'])) {
            Mage::throwException($this->_helper->__('Invalid recommendation type.'));
        }

        if ($this->_helper->getDebugMode()) {
            $this->_helper->getLogger($params);
        }

        $userId = $this->_getCustomerId();
        $cookieId = $this->_gravityCookie;
        $recommendationContext = new GravityRecommendationContext();
        $recommendationContext->scenarioId = $params['type'];

        if (isset($params['limit']) && $params['limit']) {
            $recommendationContext->numberLimit = $params['limit'];
        } else {
            $recommendationContext->numberLimit = 5;
        }

        $storeValue = new GravityNameValue('storeId', $this->_storeId);
        $recommendationContext->nameValues = array($storeValue);

        if (isset($params['itemId']) && $params['itemId']) {
            $pageItemId = new GravityNameValue('currentItemId', $params['itemId']);
            $recommendationContext->nameValues = array_merge(array($pageItemId), $recommendationContext->nameValues);
        }

        if (isset($params['itemsInCart']) && is_array($params['itemsInCart']) && $params['itemsInCart']) {
            foreach ($params['itemsInCart'] as $itemId) {
                $itemId = new GravityNameValue('cartItemId', $itemId);
                $recommendationContext->nameValues = array_merge(array($itemId), $recommendationContext->nameValues);
            }
        }

        if (isset($params['filters']) && is_array($params['filters']) && $params['filters']) {
            foreach ($params['filters'] as $key => $filter) {
                $nameValue = new GravityNameValue($key, $filter);
                $recommendationContext->nameValues = array_merge(array($nameValue), $recommendationContext->nameValues);
            }
        }

        if (isset($params['keywords']) && is_array($params['keywords']) && $params['keywords']) {
            foreach ($params['keywords'] as $key => $filter) {
                $nameValue = new GravityNameValue($key, $filter);
                $recommendationContext->nameValues = array_merge(array($nameValue), $recommendationContext->nameValues);
            }
        }

        if ($this->_canDebugLog) {
            $this->_helper->getLogger($recommendationContext->nameValues);
        }

        $recommendationContext->recommendationTime = time();

        $itemRecommendation = null;
        try {

            $itemRecommendation = $client->getItemRecommendation($userId, $cookieId, $recommendationContext);

            return $itemRecommendation;

        } catch (GravityException $e) {

            $this->_helper->getLogger($this->_helper->__('Error happened by getting the item recommendation!'));
            $this->_helper->getLogger($e->getMessage());
            $this->_helper->getLogger($e->faultInfo);

        }
    }

    /**
     * Send update
     *
     * @param GravityClient $client client
     * @param array         $params parameters
     * @throws Mage_Core_Exception
     * @return object
     */
    protected function _sendUpdate($client, $params)
    {
        if (!isset($params['type'])) {
            Mage::throwException($this->_helper->__('Invalid recommendation type.'));
        }

        try {
            $result = '';
            switch ($params['type']) {
                case self::EVENT_TYPE_CUSTOMER_UPDATE:
                    if (isset($params['userid']) && $params['userid']) {
                        $user = new GravityUser();
                        $user->userId = $params['userid'];
                        $user->hidden = false;

                        if ($this->_canDebugLog) {
                            $this->_helper->getLogger('userId: ' .$params['userid']);
                        }

                        unset($params['type']);
                        unset($params['userid']);
                        foreach ($params as $attribute => $value) {
                            if ($value) {
                                $user->nameValues[] = new GravityNameValue($attribute, $value);
                            }
                        }
                        $result = $client->addUser($user, true);

                    } else {
                        Mage::throwException($this->_helper->__('Invalid ' . $params['type'] . ' parameters'));
                    }
                    break;
                case self::EVENT_TYPE_PRODUCT_UPDATE:
                    if (isset($params['itemid']) && isset($params['title']) && $params['itemid'] && $params['title']) {
                        $item = new GravityItem();
                        $item->itemId = $params['itemid'];
                        $item->title = $params['title'];
                        $item->hidden = $params['hidden'];
                        if ($this->_canDebugLog) {
                            $this->_helper->getLogger('itemId: ' .$params['itemid']);
                        }

                        unset($params['type']);
                        unset($params['itemid']);
                        unset($params['hidden']);
                        foreach ($params as $attribute => $value) {
                            if ($value) {
                                $item->nameValues[] = new GravityNameValue($attribute, $value);
                            }
                        }
                        $isAsync = true;
                        $result = $client->addItem($item, $isAsync);

                    } else {
                        Mage::throwException($this->_helper->__('Invalid ' . $params['type'] . ' parameters'));
                    }
                    break;
                default:
                    return false;
            }

            return $result;

        } catch (GravityException $e) {

            $this->_helper->getLogger($this->_helper->__('Error happened by adding user!'));
            $this->_helper->getLogger($e->getMessage());
            $this->_helper->getLogger($e->faultInfo);

        }
    }

    /**
     * Validate result
     *
     * @param stdClass $result result
     * @return null|string|array
     */
    protected function _validateResult($result)
    {
        $answer = array();

        if ($result && $result instanceof stdClass) {

            if (!empty($result->itemIds)) {
                $answer[$result->recommendationId] = $result->itemIds;
            }

            return $answer;

        } elseif ($result && is_string($result)) {

            return $result;

        }

        return null;
    }

    /**
     * Init Gravity Client
     *
     * @return GravityClient object
     */
    protected function _init()
    {
        $config = new GravityClientConfig();
        $config->remoteUrl = $this->_getApiUrl();
        $config->user = $this->_helper->getApiUser();
        $config->password = $this->_helper->getApiPassword();
        return new GravityClient($config);
    }

    /**
     * Get current customer id
     *
     * @return mixed
     */
    protected function _getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    /**
     * Get API URL
     *
     * @param integer|string|Mage_Core_Model_Store $store store
     * @return boolean
     */
    private function _getApiUrl($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL, $store);
    }

    /**
     * Get Gravity cookie
     *
     * @return string
     */
    private function _getGravityCookie()
    {
        if (!$this->_gravityCookie) {
            $this->_gravityCookie = Mage::getModel('core/cookie')->get(self::GRAVITY_COOKIE_NAME);
        }

        return $this->_gravityCookie;
    }
}
