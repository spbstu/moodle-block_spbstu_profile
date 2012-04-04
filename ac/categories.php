<?php

define('AJAX_SCRIPT', true);

require_once('../../../config.php');

require_login();

$q = optional_param('query', '', PARAM_RAW);

echo implode("\n",
        array_map(
            function($el) { return $el->idnumber; },
            $DB->get_records_select('course_categories', 'idnumber LIKE ?', array($q.'%'),
                                    'idnumber ASC', 'DISTINCT idnumber')
        )
     );
?>
