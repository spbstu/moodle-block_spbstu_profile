<?php
/**
 * @author Dmitry Ketov <dketov@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block
 * @subpackage course_contacts
 *
 * Block: IDNumber
 */

require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('forms.php');

class block_spbstu_profile extends block_base {
    function init() {
      $this->title = get_string('usercurrentsettings', 'core'); 
    }

    function get_content () {
      global $SESSION, $OUTPUT, $CFG, $USER, $DB, $COURSE, $PAGE;

      $user = $DB->get_record('user', array('id' => $USER->id));
      $september1 = mktime(0, 0, 0, 9, 1, date("Y"));
      $september1last = mktime(0, 0, 0, 9, 1, date("Y") - 1);
      $now = time();

      $timeok = ($now > $september1) ? 
            $user->timemodified > $september1 :
            $user->timemodified > $september1last;

      if (ajaxenabled()) {
          $acjs = new moodle_url('/blocks/'.$this->name().'/autocomplete.js');
          $acgphp = new moodle_url('/blocks/'.$this->name().'/ac/groups.php');
          $accphp = new moodle_url('/blocks/'.$this->name().'/ac/categories.php');

          $PAGE->requires->yui2_lib('autocomplete');
          $PAGE->requires->js($acjs);
          $PAGE->requires->js_function_call('autocomplete', array($acgphp->out(), 'id_idnumber', 'ac_idnumber'));
          $PAGE->requires->js_function_call('autocomplete', array($accphp->out(), 'id_department', 'ac_department'));
        }
      
      ob_start();
//      profile_load_data($user);
      if(!$timeok) {
        $user->idnumber = '';
      }

      if(!trim($user->idnumber))
      {
        $form = new spbstu_profile_form($action = new moodle_url('/blocks/'.$this->name().'/save.php'));
        $form->set_data($user);

        $form->display();
      }

      $this->content->text = ob_get_contents();
      ob_end_clean();

      return $this->content;
    }

}
?>
