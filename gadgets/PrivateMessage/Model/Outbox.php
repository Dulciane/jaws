<?php
/**
 * PrivateMessage Gadget
 *
 * @category    GadgetModel
 * @package     PrivateMessage
 * @author      Mojtaba Ebrahimi <ebrahimi@zehneziba.ir>
 * @copyright   2013 Jaws Development Group
 * @license     http://www.gnu.org/copyleft/lesser.html
 */
class PrivateMessage_Model_Outbox extends Jaws_Gadget_Model
{
    /**
     * Get Outbox
     *
     * @access  public
     * @param   integer  $user      User id
     * @return  mixed    Inbox content  or Jaws_Error on failure
     */
    function GetOutbox($user)
    {
        $table = Jaws_ORM::getInstance()->table('pm_messages');
        $table->select(
            'pm_messages.id:integer','pm_messages.subject', 'pm_messages.body', 'pm_messages.insert_time',
            'users.nickname as from_nickname'
        );
        $table->join('users', 'pm_messages.from', 'users.id');
        $table->where('pm_messages.from', $user);

        $result = $table->fetchAll();
        if (Jaws_Error::IsError($result)) {
            return new Jaws_Error($result->getMessage(), 'SQL');
        }

        return $result;
    }

 }