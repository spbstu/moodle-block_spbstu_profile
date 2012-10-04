<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class spbstu_profile_form extends moodleform {

    public function definition() {
        global $CFG, $DB, $USER;
        $mform =& $this->_form;

        //$mform->addElement('header', 'moodle_picture', get_string('pictureofuser'));
        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');

        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);

        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);

        if ($field = $DB->get_record('user_info_field', array('shortname' => 'middlename'))) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $USER->id);
            $formfield->edit_field($mform);
            $mform->addRule($formfield->inputname, get_string('required'), 'required', null, 'client');
        }

        $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
        $mform->setType('department', PARAM_MULTILANG);
        $mform->addElement('html', '<div id="ac_department"></div>');
        $mform->addHelpButton('department', 'department', 'block_spbstu_profile');

        $role = $DB->get_record('role', array('shortname' => 'student'));
        $mform->addElement('header', 'student', $role->name);
        $mform->addElement('text', 'idnumber', get_string('idnumber'));
        $mform->setType('idnumber', PARAM_NOTAGS);
        //$mform->addRule('idnumber', '', 'optional', 'regex', '|^[0-9]+/[0-9]+$|', null, 'client');

        $mform->addElement('html', '<div id="ac_idnumber"></div>');
        $mform->addElement('static', 'idwarning');

        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $mform->addElement('header', 'teacher', $role->name);
        if ($field = $DB->get_record('user_info_field', array('shortname' => 'title'))) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $USER->id);
            $formfield->edit_field($mform);
        }

        $this->add_action_buttons();
    }

    public function getElement($e) {
        $mform =& $this->_form;

        return $mform->getElement($e);
    }

    public function validation($data) {
        $errors = parent::validation($data);

        if( !trim($data['profile_field_title'])
            and preg_match('|[0-9]+[/][0-9]+|', $data['idnumber']) == 0
          )
        {
           $errors['idnumber'] = get_string('idnumber_error', 'block_spbstu_profile');
           $errors['profile_field_title'] = get_string('profile_field_title_error', 'block_spbstu_profile');
        }

        foreach(array('firstname','lastname','profile_field_middlename') as $f) {
            if(preg_match('/[a-zA-Z]/', $data[$f]))
            {
                $errors[$f] = get_string('cyrillic_name_error', 'block_spbstu_profile');
            }
        }

        return $errors;
    }
}
?>
