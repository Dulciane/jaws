/**
 * ControlPanel Javascript actions
 *
 * @category    Ajax
 * @package     ControlPanel
 * @author      Ali Fazelzadeh <afz@php.net>
 * @copyright   2013-2015 Jaws Development Group
 * @license     http://www.gnu.org/copyleft/lesser.html
 */
/**
 * Use async mode, create Callback
 */
var ControlPanelCallback = {
    JawsVersion: function(response) {
        $('#latest_jaws_version').html(response);
        if (!response.blank() && response !== $('#jaws_version').val()) {
            $('#div.notify_version').css('display', 'block');
        }
    }
}

/**
 * Initiates script
 */
function init()
{
    $('#sidebar h2').on('click', function () {
        $(this).toggleClass('collapsed');
        ControlPanelStorage.update(
            $('#sidebar').children().index($(this).parent()),
            $(this).attr('class')
        );
        $(this).next('div').toggle();
    });
    $('#sidebar').children().each(function(i) {
        if (ControlPanelStorage.fetch(i)) {
            $(this).children('h2').trigger('click');
        }
    });

    // compare current version with latest jaws version
    if (!$('#latest_jaws_version').text() &&
        $('#latest_jaws_version').text() !== $('#jaws_version').val())
    {
        $('#div.notify_version').css('display', 'block');
    }

    // check jaws project website for latest version
    if ($('#do_checking').val() == 1) {
        ControlPanelAjax.callAsync('JawsVersion');
    }
}

var ControlPanelAjax = new JawsAjax('ControlPanel', ControlPanelCallback);
var ControlPanelStorage = new JawsStorage('ControlPanel');
