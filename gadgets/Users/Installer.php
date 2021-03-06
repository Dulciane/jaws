<?php
/**
 * Users Installer
 *
 * @category    GadgetModel
 * @package     Users
 * @author      Ali Fazelzadeh <afz@php.net>
 * @copyright   2012-2015 Jaws Development Group
 * @license     http://www.gnu.org/copyleft/lesser.html
 */
class Users_Installer extends Jaws_Gadget_Installer
{
    /**
     * Gadget Registry keys
     *
     * @var     array
     * @access  private
     */
    var $_RegKeys = array(
        array('latest_limit', '10'),
        array('password_recovery', 'false'),
        array('register_notification', 'true'),
        array('authtype', 'Default'),
        array('anon_register', 'false'),
        array('anon_activation', 'user'),
        array('anon_group', ''),
    );

    /**
     * Gadget ACLs
     *
     * @var     array
     * @access  private
     */
    var $_ACLKeys = array(
        'ManageUsers',
        'ManageGroups',
        'ManageOnlineUsers',
        'ManageProperties',
        'ManageUserACLs',
        'ManageGroupACLs',
        'ManageAuthenticationMethod',
        'ManageFriends',
        'AccessDashboard',
        'ManageDashboard',
        'EditUserName',
        'EditUserEmail',
        'EditUserNickname',
        'EditUserPassword',
        'EditUserPersonal',
        'EditUserContacts',
        'EditUserPreferences',
    );

    /**
     * Installs the gadget
     *
     * @access  public
     * @return  mixed   True on successful installation, Jaws_Error otherwise
     */
    function Install()
    {
        $variables = array();
        $variables['logon_hours'] = str_pad('', 42, 'F');
        $result = $this->installSchema('schema.xml', $variables);
        if (Jaws_Error::IsError($result)) {
            return $result;
        }

        $new_dir = JAWS_DATA . 'avatar';
        if (!Jaws_Utils::mkdir($new_dir)) {
            return new Jaws_Error(_t('GLOBAL_ERROR_FAILED_CREATING_DIR', $new_dir));
        }

        // Create the group 'users'
        $userModel = new Jaws_User;
        $result = $userModel->AddGroup(
            array(
                'name' => 'users',
                'title' => 'Users',
                'description' => '',
                'enabled' => true,
                'removable' => false
            )
        );
        if (Jaws_Error::IsError($result) && MDB2_ERROR_CONSTRAINT != $result->getCode()) {
            return $result;
        }

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
        if (version_compare($old, '2.0.0', '<')) {
            $variables = array();
            $variables['logon_hours'] = str_pad('', 42, 'F');
            $result = $this->installSchema('schema.xml', $variables, '1.0.0.xml');
            if (Jaws_Error::IsError($result)) {
                return $result;
            }

            // update users passwords
            $usersTable = Jaws_ORM::getInstance()->table('users');
            $usersTable->update(
                array('password' => $usersTable->concat(array('{SSHA1}', 'text'), 'password'))
            )->where($usersTable->length('password'), 32, '>')
            ->exec();
            $usersTable->update(
                array('password' => $usersTable->concat(array('{MD5}', 'text'), 'password'))
            )->where($usersTable->length('password'), 32)
            ->exec();

            // ACL keys
            $this->gadget->acl->insert('ManageFriends');
            $this->gadget->acl->insert('AccessDashboard');
            $this->gadget->acl->insert('ManageDashboard');
        }

        if (version_compare($old, '2.1.0', '<')) {
            $this->gadget->registry->delete('anon_repetitive_email');
        }

        return true;
    }

}