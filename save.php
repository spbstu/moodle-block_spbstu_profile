<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('forms.php');

require_login();

$userid = $USER->id;

if (!$user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}

$form = new spbstu_profile_form();
if($formdata = $form->get_data())
{
  $user->idnumber = trim($formdata->idnumber);
  $user->department = trim($formdata->department);
  $user->profile_field_title = trim($formdata->profile_field_title);

  profile_save_data($user);
  $DB->update_record('user', $user);
}

redirect($CFG->wwwroot);

?>
