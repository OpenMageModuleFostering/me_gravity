<?php
/**
 * Class Me_Gravity_Block_CatalogSearch_Boxes_Result
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Block_CatalogSearch_Boxes_Result
 */
class Me_Gravity_Block_Catalogsearch_Boxes_Result extends Me_Gravity_Block_Recommendation
{
    /**
     * @var array|null
     */
    protected $_keywords = null;

    /**
     * @var string
     */
    protected $_boxClass = 'search';

    /**
     * @var string
     */
    protected $_pageType = 'search';

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $boxHelper = $this->_getGravityBoxHelper();

        $this->setRecommendationType(Me_Gravity_Model_Method_Request::SEARCH_RESULT_PAGE);

        $boxTitle = $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            ? $boxHelper->getBoxTitle($this->_boxClass, $this->_pageType)
            : $this->getGravityHelper()->__('Recommended for you');
        $this->setRecommendationTitle($boxTitle);

        $this->setRecommendationLimit($boxHelper->getBoxLimit($this->_boxClass, $this->_pageType));
        $this->setBoxColumnCount($boxHelper->getBoxColumns($this->_boxClass, $this->_pageType));

        $this->_setSearchKeywords();

        parent::_construct();
    }

    /**
     * Get recommended items
     *
     * @return $this|bool
     */
    public function getProductCollection()
    {
        if ($this->getRecommendationType()) {

            $items = Mage::getModel('me_gravity/method_request')->sendRequest(
                Me_Gravity_Model_Method_Request::EVENT_TYPE_GET,
                array(
                    'type' => $this->_recommendationType,
                    'limit' => $this->_recommendationLimit,
                    'keywords' => $this->_keywords ? $this->_keywords : null
                )
            );

            if (!empty($items)) {

                $this->_recommendationId = key($items);
                $itemCollection = $this->_getProductCollection();
                $itemCollection->addAttributeToFilter('entity_id', array('in' => $items));
                $itemCollection->load();

            } else {

                $itemCollection = null;

            }

            return $itemCollection;

        } else {

            return null;

        }
    }

    /**
     * Get search keywords
     *
     * @return string
     */
    public function getSearchKeyword()
    {
        return Mage::helper('catalogsearch')->getEscapedQueryText();
    }

    /**
     * Get search criterias
     *
     * @return array
     */
    public function getSearchCriterias()
    {
        return $this->getRequest()->getQuery();
    }

    /**
     * Get all search keyword
     *
     * @return array
     */
    public function getSearchKeywords()
    {
        return $this->_keywords;
    }

    /**
     * Set current search keywords
     *
     * @return array
     * @throws Exception
     */
    protected function _setSearchKeywords()
    {
        try {

            if (is_null($this->_keywords)) {
                if ($this->getSearchKeyword()) {
                    $this->_keywords['searchKeyword'] = $this->getSearchKeyword();
                }
                $searchCriterias = $this->getSearchCriterias();
                if (isset($searchCriterias['q'])) {
                    unset($searchCriterias['q']);
                }
                if (!empty($searchCriterias)) {
                    foreach ($searchCriterias as $key => $criteria) {
                        if ($key == 'price') {

                            if (isset($criteria['from'])) {
                                $this->_keywords['minPrice'] = $criteria['from'];
                            }
                            if (isset($criteria['to'])) {
                                $this->_keywords['maxPrice'] = $criteria['to'];
                            }

                        } else {
                            if (is_array($criteria)) {
                                $this->_keywords[$key] = $this->escapeHtml(strtolower(implode(',', $criteria)));
                            } else {
                                $this->_keywords[$key] = $this->escapeHtml(strtolower($criteria));
                            }

                        }

                    }
                }

            }

            return $this->_keywords;

        } catch (Mage_Core_Exception $e) {
            $this->getGravityHelper()->getLogger($e->getMessage());
        } catch (Exception $e) {
            $this->getGravityHelper()->getLogger(
                $e->getMessage(),
                $this->getGravityHelper()->__('An error occurred while setting search keywords.')
            );
        }
    }
}
