<?php
/**
 * EventsCalendar Gadget
 *
 * @category    Gadget
 * @package     EventsCalendar
 * @author      Mohsen Khahani <mkhahani@gmail.com>
 * @copyright   2013 Jaws Development Group
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
$GLOBALS['app']->Layout->AddHeadLink('gadgets/EventsCalendar/Resources/site_style.css');
class EventsCalendar_Actions_ViewEvent extends Jaws_Gadget_Action
{
    /**
     * Displays an event
     *
     * @access  public
     * @return  string  XHTML UI
     */
    function ViewEvent()
    {
        $id = (int)jaws()->request->fetch('id', 'get');
        $model = $this->gadget->loadModel('Event');
        $user = (int)$GLOBALS['app']->Session->GetAttribute('user');
        $event = $model->GetEvent($id, $user);
        if (Jaws_Error::IsError($event) ||
            empty($event) ||
            $event['user'] != $user)
        {
            return;
        }

        $jdate = $GLOBALS['app']->loadDate();
        $tpl = $this->gadget->loadTemplate('ViewEvent.html');
        $tpl->SetBlock('event');

        // Menubar
        $action = $this->gadget->loadAction('Menubar');
        $tpl->SetVariable('menubar', $action->Menubar('Events'));

        // Subject
        $tpl->SetVariable('title', $event['subject']);
        $this->SetTitle($event['subject']);

        // Location
        $tpl->SetVariable('location', $event['location']);
        $tpl->SetVariable('lbl_location', _t('EVENTSCALENDAR_EVENT_LOCATION'));

        // Description
        $tpl->SetVariable('desc', $event['description']);
        $tpl->SetVariable('lbl_desc', _t('EVENTSCALENDAR_EVENT_DESC'));

        // Date
        $start_date = empty($event['start_date'])? '' :
            $jdate->Format($event['start_date'], 'Y-m-d');
        $stop_date = empty($event['stop_date'])? '' :
            $jdate->Format($event['stop_date'], 'Y-m-d');
        $tpl->SetVariable('start_date', $start_date);
        $tpl->SetVariable('stop_date', $stop_date);
        $tpl->SetVariable('lbl_date', _t('EVENTSCALENDAR_DATE'));

        // Time
        $start_time = round($event['start_time'] / 3600);
        $stop_time = round($event['stop_time'] / 3600);
        $tpl->SetVariable('start_time', $start_time);
        $tpl->SetVariable('stop_time', $stop_time);
        $tpl->SetVariable('lbl_time', _t('EVENTSCALENDAR_TIME'));

        // Type
        $tpl->SetVariable('type', $event['type']);
        $tpl->SetVariable('lbl_type', _t('EVENTSCALENDAR_EVENT_TYPE'));

        // Priority
        $tpl->SetVariable('priority', _t('EVENTSCALENDAR_EVENT_PRIORITY_'.$event['priority']));
        $tpl->SetVariable('lbl_priority', _t('EVENTSCALENDAR_EVENT_PRIORITY'));

        // Reminder
        $tpl->SetVariable('reminder', _t('EVENTSCALENDAR_EVENT_REMINDER_'.$event['reminder']));
        $tpl->SetVariable('lbl_reminder', _t('EVENTSCALENDAR_EVENT_REMINDER'));

        // Repeat
        $tpl->SetVariable('repeat', '');
        $tpl->SetVariable('lbl_repeat', _t('EVENTSCALENDAR_EVENT_REPEAT'));

        // Actions
        $site_url = $GLOBALS['app']->GetSiteURL('/');
        $tpl->SetVariable('url_edit', $site_url . $this->gadget->urlMap('EditEvent', array('id' => $id)));
        $tpl->SetVariable('lbl_edit', _t('GLOBAL_EDIT'));

        $tpl->ParseBlock('event');
        return $tpl->Get();
    }
}