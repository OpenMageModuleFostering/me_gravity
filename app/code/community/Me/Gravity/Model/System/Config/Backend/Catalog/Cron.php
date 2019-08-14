<?php
/**
 * Class Me_Gravity_Model_System_Config_Backend_Catalog_Cron
 *
 * @category  Me
 * @package   Me_Gravity
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Gravity_Model_System_Config_Backend_Catalog_Cron
 */
class Me_Gravity_Model_System_Config_Backend_Catalog_Cron extends Mage_Core_Model_Config_Data
{
    /**
     * Path to store config cron job schedule time
     *
     * @var string
     */
    const CRON_STRING_PATH = 'crontab/jobs/me_gravity_catalog_generate/schedule/cron_expr';

    /**
     * Path to store config catalog export cron model
     *
     * @var string
     */
    const CRON_MODEL_PATH = 'crontab/jobs/me_gravity_catalog_generate/run/model';

    /**
     * Set cron expression after config save
     *
     * @return Mage_Core_Model_Abstract|void
     * @throws Exception
     */
    protected function _afterSave()
    {
        $enabled = $this->getData('groups/export/fields/catalog_cron/value');

        if ($enabled) {

            $time = $this->getData('groups/export/fields/catalog_cron_time/value');

            $cronExprArray = array(
                intval($time[1]), // Minute
                intval($time[0]), // Hour
                '*', // Day of the Month
                '*', // Month of the Year
                '*', // Day of the Week
            );

            $cronExprString = join(' ', $cronExprArray);

            try {
                Mage::getModel('core/config_data')
                    ->load(self::CRON_STRING_PATH, 'path')
                    ->setValue($cronExprString)
                    ->setPath(self::CRON_STRING_PATH)
                    ->save();
                Mage::getModel('core/config_data')
                    ->load(self::CRON_MODEL_PATH, 'path')
                    ->setValue((string)Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                    ->setPath(self::CRON_MODEL_PATH)
                    ->save();
            } catch (Exception $e) {
                throw new Exception(Mage::helper('me_gravity')->__('Unable to save the cron catalog export expression.'));
            }

        }
    }
}
