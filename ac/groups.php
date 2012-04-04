<?php

define('AJAX_SCRIPT', true);

require_once('../../../config.php');

require_login();

$q = optional_param('query', '', PARAM_RAW);

echo implode("\n", 
        array_map(
            function($el) { return $el->name; },
            $DB->get_records_select('groups', 'name LIKE ?', array($q.'%'), 'name ASC', 'DISTINCT name')
          )
     );
?>
