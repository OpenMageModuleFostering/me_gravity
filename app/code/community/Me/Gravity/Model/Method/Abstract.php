<?php
/**
 * Class Me_Gravity_Model_Method_Abstract
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_Method_Abstract
 */
abstract class Me_Gravity_Model_Method_Abstract extends Varien_Object
{
    /**
     * @var Me_Gravity_Helper_Data
     */
    protected $_helper;

    /**
     * Communication method features
     *
     * @var bool
     */
    protected $_canDebugLog = false;
    protected $_canSendRequest = false;

    /**
     * Check debug logging availability
     *
     * @return bool
     */
    public function canDebugLog()
    {
        return $this->_canDebugLog;
    }

    /**
     * Check send request availability
     *
     * @return bool
     */
    public function canSendRequest()
    {
        return $this->_canSendRequest;
    }

    /**
     * Send data
     *
     * @param string $type   event type
     * @param array  $params parameters
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function sendRequest($type, $params = array())
    {
        if (!$this->canSendRequest()) {
            Mage::throwException($this->_getGravityHelper()->__('Send action is not available.'));
        }

        return $this;
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
}
