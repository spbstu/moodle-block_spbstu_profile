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

      $PAGE->requires->yui2_lib('autocomplete');

      $form = new spbstu_profile_form($action = new moodle_url('/blocks/'.$this->name().'/save.php'));
      $userid = $USER->id;

      if (!$user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
      }
      profile_load_data($user);
      
      ob_start();
// FIXME: profile_field_<shrotname> hardcoded 'title'
      $profile_field_title = $DB->get_record('user_info_field', array('shortname' => 'title'));
      if(trim($user->idnumber) && trim($user->department) && trim($user->profile_field_title))
      {
        include('info.html');
      }
      else
      {
        $form->set_data($user);

        $form->display();

        if (ajaxenabled()) {
            $acjs = new moodle_url('/blocks/'.$this->name().'/autocomplete.js');
            $acgphp = new moodle_url('/blocks/'.$this->name().'/ac/groups.php');
            $accphp = new moodle_url('/blocks/'.$this->name().'/ac/categories.php');
            $PAGE->requires->js($acjs);
            $PAGE->requires->js_function_call('autocomplete', array($acgphp->out(), 'id_idnumber', 'ac_idnumber'));
            $PAGE->requires->js_function_call('autocomplete', array($accphp->out(), 'id_department', 'ac_department'));
        }
      }

      $this->content->text = ob_get_contents();
      ob_end_clean();

      return $this->content;
    }
}
?>
