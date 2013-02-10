<?php
/**
 * Search - URL List gadget hook
 *
 * @category   GadgetHook
 * @package    Search
 * @author     Ali Fazelzadeh <afz@php.net>
 * @copyright  2007-2013 Jaws Development Group
 * @license    http://www.gnu.org/copyleft/gpl.html
 */
class SearchURLListHook
{
    /**
     * Returns an array with all possible items the Menu gadget can use
     *
     * @access  public
     * @return  array   List of menu items
     */
    function Hook()
    {
        $urls[] = array('url'    => $GLOBALS['app']->Map->GetURLFor('Search', 'Box'),
                        'title'  => _t('SEARCH_ACTIONS_BOX'),
                        'title2' => _t('SEARCH_NAME'));
        $urls[] = array('url'    => $GLOBALS['app']->Map->GetURLFor('Search', 'SimpleBox'),
                        'title'  => _t('SEARCH_ACTIONS_SIMPLEBOX'),
                        'title2' => _t('SEARCH_NAME'));
        $urls[] = array('url'    => $GLOBALS['app']->Map->GetURLFor('Search', 'AdvancedBox'),
                        'title'  => _t('SEARCH_ACTIONS_ADVANCEDBOX'),
                        'title2' => _t('SEARCH_NAME'));
        return $urls;
    }
}
