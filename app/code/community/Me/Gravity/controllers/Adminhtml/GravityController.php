<?php
/**
 * Class Me_Gravity_Adminhtml_GravityController
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Adminhtml_GravityController
 */
class Me_Gravity_Adminhtml_GravityController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check access (in the ACL) for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/gravity');
    }

    /**
     * Export catalog action
     *
     * @return void
     */
    public function exportcatalogAction()
    {
        try {
            $gravityHelper = $this->_getGravityHelper();

            $timeStart = microtime(true);
            $resultFileName = Mage::getModel('me_gravity/products')->generateCatalogXml();
            $timeEnd = microtime(true);

            if ($gravityHelper->getDebugMode()) {
                $gravityHelper->getLogger($this->_getGravityHelper()->__('Catalog export finished in %s seconds', $timeEnd - $timeStart));
            }

            $path = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
                . $gravityHelper->getExportFilePath() . $resultFileName;
            $this->_getSession()->addSuccess(Mage::helper('me_gravity')->__('Catalog was successfully exported to %s.', $path));

        } catch (Mage_Core_Exception $e) {

            $this->_getSession()->addError($e->getMessage());

        } catch (Exception $e) {

            $this->_getSession()->addException(
                $e,
                $this->_getGravityHelper()->__('An error occurred while exporting catalog.')
            );

        }

        $this->_redirectReferer();
    }

    /**
     * Customers export action
     *
     * @return void
     */
    public function exportcustomerAction()
    {
        $store = $this->getRequest()->getParam('store');

        try {
            $storeId = Mage::app()->getStore($store)->getId();
            $gravityHelper = $this->_getGravityHelper();

            if (!$storeId) {
                $storeId = Mage::app()->getDefaultStoreView()->getId();
            }

            $timeStart = microtime(true);

            $fileName = 'gravity_customers.tsv';
            $tsvFile = Mage::getModel('me_gravity/customers')->generateCustomersTsv($storeId);

            $timeEnd = microtime(true);

            if ($gravityHelper->getDebugMode()) {
                $gravityHelper->getLogger($this->_getGravityHelper()->__('Customer export finished in %s seconds', $timeEnd - $timeStart));
            }

            $this->_prepareDownloadResponse($fileName, $tsvFile);

        } catch (Mage_Core_Exception $e) {

            $this->_getSession()->addError($e->getMessage());

        } catch (Exception $e) {

            $this->_getSession()->addException(
                $e,
                $this->_getGravityHelper()->__('An error occurred while exporting customers.')
            );

        }

        $this->_redirectReferer();
    }

    /**
     * Test connection action
     *
     * @return void
     */
    public function testAction()
    {
        $result = array(
            'success' => 0,
            'message' => ''
        );

        try {

            $answer = Mage::getModel('me_gravity/method_request')->sendRequest(Me_Gravity_Model_Method_Request::EVENT_TYPE_TEST);
            if ('Hello ' . $this->_getGravityHelper()->getApiUser() == $answer) {
                $result['success'] = 1;
                $result['message'] = $this->_getGravityHelper()->__('Connection successfully tested!');
            }

        } catch (Mage_Core_Exception $e) {

            $result['message'] = $e->getMessage() . ' ' . $this->_getGravityHelper()->__('Connection test failed. The Page will reload automatically!');
            $this->_getGravityHelper()->getLogger($e->getMessage());

        } catch (Exception $e) {

            $result['message'] = $this->_getGravityHelper()->__('An error occurred while testing connection. The Page will reload automatically!');
            $this->_getGravityHelper()->getLogger($e);

        }

        Mage::app()->getResponse()->setBody(json_encode($result));
    }

    /**
     * Get Gravity extension helper
     *
     * @return Me_Gravity_Helper_Data
     */
    protected function _getGravityHelper()
    {
        return Mage::helper('me_gravity');
    }
}
