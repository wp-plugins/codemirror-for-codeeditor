<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {exit();}
delete_option('cfc_setting_opt');
delete_option('cfc_checkver_stamp');
delete_option('cfc_updated');
?>
