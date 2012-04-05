<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class spbstu_profile_form extends moodleform {

    public function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;

        $role = $DB->get_record('role', array('shortname' => 'student'));
        $mform->addElement('header', 'student', $role->name);
        $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
        $mform->setType('department', PARAM_MULTILANG);
        $mform->addElement('html', '<div id="ac_department"></div>');

/*
        if ($field = $DB->get_record('user_info_field', array('shortname' => 'title'))) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id);
            $formfield->edit_field($mform);
        }
*/

        $mform->addElement('text', 'idnumber', get_string('idnumber'));
        $mform->setType('idnumber', PARAM_NOTAGS);
        $mform->addElement('html', '<div id="ac_idnumber"></div>');
        $mform->addElement('static', 'idwarning');

        $this->add_action_buttons();
    }
}
?>
