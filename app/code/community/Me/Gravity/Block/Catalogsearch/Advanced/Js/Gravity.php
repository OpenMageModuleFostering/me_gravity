<?php
/**
 * Class Me_Gravity_Block_Catalogsearch_Advanced_Js_Gravity
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_Catalogsearch_Advanced_Js_Gravity
 */
class Me_Gravity_Block_Catalogsearch_Advanced_Js_Gravity extends Mage_CatalogSearch_Block_Advanced_Result
{
    /**
     * Get extension is properly enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getGravityHelper()->isFullyEnabled();
    }

    /**
     * Get modified search criterias
     *
     * @return array
     */
    public function getModifiedSearchCriterias()
    {
        $modifiedCriterias = array();

        $searchCriterias = $this->getSearchCriterias();
        $parameters = $this->_cleanSearchParams($this->getParameters());
        if (!empty($searchCriterias) && !empty($parameters)) {

            $i = 0;
            foreach ($parameters as $key => $parameter) {
                $modifiedCriterias[$key] = $searchCriterias[$i];
                $i++;
            }
        }

        return $modifiedCriterias;
    }

    /**
     * Get parameters
     *
     * @return array
     * @throws Exception
     */
    public function getParameters()
    {
        return $this->getRequest()->getParams();
    }

    /**
     * Get search criterias
     *
     * @return array
     */
    public function getSearchCriterias()
    {
        return $this->getSearchModel()->getSearchCriterias();
    }

    /**
     * Get current customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }

    /**
     * Get current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * Get Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    public function getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }

    /**
     * Clean parameters form unnecessary values
     *
     * @param array $parameters parameters
     * @return array
     */
    protected function _cleanSearchParams($parameters = array())
    {
        if ($parameters) {
            if (isset($parameters['___store'])) {
                unset($parameters['___store']);
            }
            if (isset($parameters['___from_store'])) {
                unset($parameters['___from_store']);
            }

            $removeKeys = array();
            foreach ($parameters as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['from']) && isset($value['to'])) {
                        if (empty($value['from']) && empty($value['to'])) {
                            $removeKeys[] = $key;
                        }
                    }
                } else {
                    if (empty($value)) {
                        $removeKeys[] = $key;
                    }
                }
            }

            if ($removeKeys) {
                foreach ($removeKeys as $key) {
                    unset($parameters[$key]);
                }
            }
        }

        return $parameters;
    }
}
