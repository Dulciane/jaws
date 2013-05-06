<?php
/**
 * Weather Installer
 *
 * @category    GadgetModel
 * @package     Weather
 * @author      Ali Fazelzadeh <afz@php.net>
 * @copyright   2012-2013 Jaws Development Group
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Weather_Installer extends Jaws_Gadget_Installer
{
    /**
     * Installs the gadget
     *
     * @access  public
     * @return  mixed   True on successful installation, Jaws_Error otherwise
     */
    function Install()
    {
        if (!Jaws_Utils::is_writable(JAWS_DATA)) {
            return new Jaws_Error(_t('GLOBAL_ERROR_FAILED_DIRECTORY_UNWRITABLE', JAWS_DATA), _t('WEATHER_NAME'));
        }

        $new_dir = JAWS_DATA . 'weather' . DIRECTORY_SEPARATOR;
        if (!Jaws_Utils::mkdir($new_dir)) {
            return new Jaws_Error(_t('GLOBAL_ERROR_FAILED_CREATING_DIR', $new_dir), _t('WEATHER_NAME'));
        }

        $result = $this->installSchema('schema.xml');
        if (Jaws_Error::IsError($result)) {
            return $result;
        }

        // Registry keys
        $this->gadget->registry->insert('unit', 'metric');
        $this->gadget->registry->insert('date_format', 'DN d MN');
        $this->gadget->registry->insert('update_period', '3600');
        $this->gadget->registry->insert('api_key', '');

        return true;
    }

    /**
     * Uninstalls the gadget
     *
     * @access  public
     * @return  mixed   True on success, Jaws_Error otherwise
     */
    function Uninstall()
    {
        $result = $GLOBALS['db']->dropTable('weather');
        if (Jaws_Error::IsError($result)) {
            $gName  = _t('WEATHER_NAME');
            $errMsg = _t('GLOBAL_ERROR_GADGET_NOT_UNINSTALLED', $gName);
            $GLOBALS['app']->Session->PushLastResponse($errMsg, RESPONSE_ERROR);
            return new Jaws_Error($errMsg, $gName);
        }

        // Registry keys
        $this->gadget->registry->delete('unit');
        $this->gadget->registry->delete('date_format');
        $this->gadget->registry->delete('update_period');
        $this->gadget->registry->delete('api_key');

        return true;
    }

    /**
     * Upgrades the gadget
     *
     * @access  public
     * @param   string  $old    Current version (in registry)
     * @param   string  $new    New version (in the $gadgetInfo file)
     * @return  mixed   True on success, Jaws_Error otherwise
     */
    function Upgrade($old, $new)
    {
        if (version_compare($old, '0.8.0', '<')) {
            $result = $this->installSchema('schema.xml');
            if (Jaws_Error::IsError($result)) {
                return $result;
            }

            // Remove from layout
            $layoutModel = $GLOBALS['app']->loadGadget('Layout', 'AdminModel');
            if (!Jaws_Error::isError($layoutModel)) {
                $layoutModel->DeleteGadgetElements('Weather');
            }

            // ACL keys
            $this->gadget->acl->insert('ManageRegions');
            $this->gadget->acl->delete('AddCity');
            $this->gadget->acl->delete('EditCity');
            $this->gadget->acl->delete('DeleteCity');

            // Registry keys
            $this->gadget->registry->insert('unit', 'metric');
            $this->gadget->registry->insert('date_format', 'DN d MN');
            $this->gadget->registry->insert('update_period', '3600');
            $this->gadget->registry->delete('refresh');
            $this->gadget->registry->delete('cities');
            $this->gadget->registry->delete('units');
            $this->gadget->registry->delete('forecast');
            $this->gadget->registry->delete('partner_id');
            $this->gadget->registry->delete('license_key');
        }

        // Registry keys
        $this->gadget->registry->insert('api_key', '');

        return true;
    }

}