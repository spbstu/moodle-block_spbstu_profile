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
require_once($CFG->dirroot.'/user/editlib.php');
require_once('forms.php');

class block_spbstu_profile extends block_base {
    function init() {
      $this->title = get_string('usercurrentsettings', 'core'); 
    }

    function get_content () {
      global $SESSION, $OUTPUT, $CFG, $USER, $DB, $COURSE, $PAGE;

      if($this->content) return $this->content;

      $this->content = new stdClass;
      if(isguestuser()) {
        $this->content->text = '';
        return $this->content;
      }

      $user = $DB->get_record('user', array('id' => $USER->id));

      $context = get_context_instance(CONTEXT_USER, $user->id, MUST_EXIST);
      $fs = get_file_storage();
      $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') or
                             $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
 
      $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size'=>64));
//http://dl.spbstu.ru/user/editadvanced.php?id=3#moodle_picture

      $september1 = mktime(0, 0, 0, 9, 1, date("Y"));
      $september1last = mktime(0, 0, 0, 9, 1, date("Y") - 1);
      $now = time();

      $timeok = ($now > $september1) ? 
            $user->timemodified > $september1 :
            $user->timemodified > $september1last;

      if(!$timeok and empty($user->profile['title'])) {
        $user->idnumber = '';
      }

      profile_load_data($user);
      $form = new spbstu_profile_form(); //$action = new moodle_url('/blocks/'.$this->name().'/save.php'));
      $form->getElement('currentpicture')->setValue($imagevalue);
      $form->set_data($user);

      ob_start();
      if($form->validation((array)$user) and !$form->is_validated()) {
        $form->display();
      }
      else {
        if($formdata = $form->get_data()) {     
          foreach(array('idnumber', 'department', 'firstname', 'lastname',
                        'profile_field_middlename', 'profile_field_title') as $f) {
            $user->{$f} = trim($formdata->{$f});
          }
          $user->timemodified = time();

          $DB->update_record('user', $user);
          profile_save_data($user);
        }        

        profile_load_custom_fields($user);
        $tidn = $user->profile['title'].' '.$user->department;
        if( (empty($user->idnumber) or $user->idnumber != $tidn) and !empty($user->profile['title']) ) {
          $user->idnumber = $tidn;
          $DB->update_record('user', $user);

          $this->notify($user);
        }
      }

      $this->content->text = ob_get_contents();
      ob_end_clean();

      return $this->content;
    }

    function get_required_javascriptttt() {
      $acjs = new moodle_url('/blocks/'.$this->name().'/autocomplete.js');
      $acgphp = new moodle_url('/blocks/'.$this->name().'/ac/groups.php');
      $accphp = new moodle_url('/blocks/'.$this->name().'/ac/categories.php');

      $this->page->requires->yui2_lib('autocomplete');
      $this->page->requires->js($acjs);
      $this->page->requires->js_function_call('autocomplete', array($acgphp->out(), 'id_idnumber', 'ac_idnumber'));
      $this->page->requires->js_function_call('autocomplete', array($accphp->out(), 'id_department', 'ac_department')); 
    }

    private function notify($user) {
      global $CFG;
      if($users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse')) {
        foreach ($users as $u) {
          $eventdata = new stdClass();
          $eventdata->component         = 'moodle';
          $eventdata->name              = 'instantmessage';
          $eventdata->userfrom          = $user;
          $eventdata->userto            = $u;
          $eventdata->subject           = 'Новый преподаватель';
          $eventdata->fullmessage       = 'Новый преподаватель: '.fullname($user).', '.$user->idnumber;
          $eventdata->fullmessageformat = FORMAT_PLAIN;
          $eventdata->fullmessagehtml   = '';
          $eventdata->smallmessage      = '';
          $eventdata->notification      = 1;
          message_send($eventdata);
        }
      }
    }

public function applicable_formats() {
  return array(
           'my-index' => true,
  );
} 

}
?>
