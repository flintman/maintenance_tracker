<?php
if (!defined('MAINTENANCE_TRACKER_INIT')) {
    http_response_code(403);
    exit('Direct access not permitted.');
}

// English language strings (grouped and normalized)
// Keep this file tidy: group related keys and provide legacy aliases for backward compatibility.

// ----- App / Meta -----
$smarty->assign('APP_TITLE', 'Maintenance Tracker');
$smarty->assign('VERSION_TITLE', 'Version');
$smarty->assign('FOOTER_TITLE', 'Maintenance System');

// ----- Global buttons & actions -----
$smarty->assign('BTN_SAVE', 'Save');
$smarty->assign('BTN_CANCEL', 'Cancel');
$smarty->assign('BTN_SUBMIT', 'Submit');
$smarty->assign('BTN_DELETE', 'Delete');
// Backwards-compatible aliases (older templates may still use these)
$smarty->assign('SAVE_TITLE', $smarty->getTemplateVars('BTN_SAVE'));
$smarty->assign('CANCEL_BUTTON', $smarty->getTemplateVars('BTN_CANCEL'));
$smarty->assign('SUBMIT_BUTTON', $smarty->getTemplateVars('BTN_SUBMIT'));
$smarty->assign('DELETE_BUTTON', $smarty->getTemplateVars('BTN_DELETE'));

// ----- Navigation / UI labels -----
$smarty->assign('LIGHT_MODE_LABEL', 'Light Mode');
$smarty->assign('DARK_MODE_LABEL', 'Dark Mode');
$smarty->assign('NAV_USER_INFO', 'User Info');
$smarty->assign('LOGOUT_LABEL', 'Logout');

// ----- Themes / Appearance -----
$smarty->assign('DEFAULT_THEME_TITLE', 'Default Theme');
$smarty->assign('THEME_TITLE', 'Theme');
$smarty->assign('TOGGLE_THEME_TITLE', 'Toggle Theme');

// ----- Authentication -----
$smarty->assign('LOGIN_TITLE', 'Login');
$smarty->assign('LOGIN_BUTTON', 'Login');
$smarty->assign('USERNAME_TITLE', 'Username');
$smarty->assign('PASSWORD_TITLE', 'Password');
$smarty->assign('CONFIRM_PASSWORD_TITLE', 'Confirm Password');

// ----- Admin: Messages -----
$smarty->assign('ADMIN_MANAGE_MESSAGE_TITLE', 'Manage Message Board');
$smarty->assign('ADMIN_ADD_MESSAGE_TITLE', 'Add New Message');
$smarty->assign('ADMIN_MESSAGE_CONTENT_LABEL', 'Message Content');
$smarty->assign('ADMIN_MESSAGE_PLACEHOLDER', 'Enter your message here...');
$smarty->assign('ADMIN_ADD_MESSAGE_BUTTON', 'Add Message');
$smarty->assign('ADMIN_ALL_MESSAGES_TITLE', 'All Messages');
$smarty->assign('ADMIN_ACTIVE_FIRST_NOTE', 'Active message is displayed first, followed by others in date order (newest first)');
$smarty->assign('ADMIN_STATUS', 'Status');
$smarty->assign('ADMIN_MESSAGE', 'Message');
$smarty->assign('ADMIN_DATE_CREATED', 'Date Created');
$smarty->assign('ADMIN_ACTIONS', 'Actions');
$smarty->assign('ADMIN_ACTIVE', 'Active');
$smarty->assign('ADMIN_INACTIVE', 'Inactive');
$smarty->assign('ADMIN_ACTIVATE_CONFIRM', 'Activate this message? This will deactivate the current active message.');
$smarty->assign('ADMIN_ACTIVATE_BUTTON', 'Activate');
$smarty->assign('ADMIN_EDIT_BUTTON', 'Edit');
$smarty->assign('ADMIN_DELETE_CONFIRM', 'Are you sure you want to delete this message? This action cannot be undone.');
$smarty->assign('ADMIN_DELETE_BUTTON', $smarty->getTemplateVars('BTN_DELETE'));
$smarty->assign('ADMIN_MESSAGE_ADDED', 'Message added successfully!');
$smarty->assign('ADMIN_ERROR_ADDING_MESSAGE', 'Error adding message:');
$smarty->assign('ADMIN_MESSAGE_EMPTY', 'Message cannot be empty.');
$smarty->assign('ADMIN_MESSAGE_UPDATED', 'Message updated successfully!');
$smarty->assign('ADMIN_ERROR_UPDATING_MESSAGE', 'Error updating message:');
$smarty->assign('ADMIN_INVALID_ID_OR_EMPTY', 'Invalid message ID or empty message.');
$smarty->assign('ADMIN_MESSAGE_ACTIVATED', 'Message activated successfully!');
$smarty->assign('ADMIN_ERROR_ACTIVATING_MESSAGE', 'Error activating message:');
$smarty->assign('ADMIN_INVALID_MESSAGE_ID', 'Invalid message ID.');
$smarty->assign('ADMIN_MESSAGE_DELETED', 'Message deleted successfully!');
$smarty->assign('ADMIN_ERROR_DELETING_MESSAGE', 'Error deleting message:');

// ----- Admin: Dashboard (admin/index.php) -----
$smarty->assign('ADMIN_PASSWORDS_DO_NOT_MATCH', 'Passwords do not match.');
$smarty->assign('ADMIN_PASSWORD_TOO_SHORT', 'Password must be at least 6 characters.');
$smarty->assign('ADMIN_USER_ADDED', 'User added!');
$smarty->assign('ADMIN_ERROR_ADDING_USER', 'Error adding user:');

// ----- Admin: Manage Users (manage_users.php) -----
$smarty->assign('USER_PASSWORD_TOO_SHORT', 'Password must be at least 6 characters.');
$smarty->assign('USER_UPDATED_PASSWORD', 'User updated (password changed)!');
$smarty->assign('USER_UPDATED', 'User updated!');
$smarty->assign('USER_DELETED', 'User deleted!');

// ----- Admin: Navigation / Labels -----
$smarty->assign('ADMIN_PANEL_TITLE', 'Admin Panel');
$smarty->assign('ADMIN_MANAGE_LABEL', 'Manage');
$smarty->assign('ADMIN_DASHBOARD_TITLE', 'Admin Dashboard');
$smarty->assign('ADMIN_MANAGE_USERS_TITLE', 'Manage Users');
$smarty->assign('USERS_TITLE', 'Users');
$smarty->assign('ADMIN_ALL_USERS_TITLE', 'All Users');
$smarty->assign('PRIVILEGE_TITLE', 'Privilege');
$smarty->assign('ADMIN_MANAGE_MESSAGES_TITLE', 'Manage Messages');
$smarty->assign('ADMIN_SITE_CONFIG', 'Site Config');
$smarty->assign('ADMIN_USER_DASHBOARD', 'User Dashboard');

// ----- Admin: Footer / Empty states / Config -----
$smarty->assign('ADMIN_NO_MESSAGES_TITLE', 'No Messages');
$smarty->assign('ADMIN_NO_MESSAGES_DESC', 'There are currently no messages to display.');
$smarty->assign('NO_MESSAGES_AT_THIS_TIME', 'No messages at this time.');

// Authentication error messages
$smarty->assign('INVALID_CREDENTIALS', 'Invalid credentials');

// Maintenance / Equipment messages
$smarty->assign('NO_EQUIPMENT_SELECTED', 'No equipment selected.');

// Maintenance success messages
$smarty->assign('MAINTENANCE_RECORD_ADDED', 'Maintenance record added successfully!');

// User account success messages
$smarty->assign('API_KEY_GENERATED', 'A new API key has been generated.');
$smarty->assign('EMAIL_UPDATED_SUCCESS', 'Email updated successfully.');
$smarty->assign('NICKNAME_UPDATED_SUCCESS', 'Nickname updated successfully.');
$smarty->assign('THEME_UPDATED_SUCCESS', 'Theme updated successfully.');
$smarty->assign('PASSWORD_UPDATED_SUCCESS', 'Password updated successfully.');

// User error messages
$smarty->assign('INVALID_EMAIL_ADDRESS', 'Invalid email address.');
$smarty->assign('NICKNAME_TOO_LONG', 'Nickname must be 50 characters or less.');
$smarty->assign('CURRENT_PASSWORD_INCORRECT', 'Current password is incorrect.');
$smarty->assign('NEW_PASSWORDS_MISMATCH', 'New passwords do not match.');
$smarty->assign('NEW_PASSWORD_TOO_SHORT', 'New password must be at least 8 characters long.');

// Admin messages
$smarty->assign('CONFIGURATION_UPDATED', 'Configuration updated!');

// View maintenance / photo alerts
$smarty->assign('NO_MAINTENANCE_SELECTED', 'No maintenance record selected.');
$smarty->assign('MAINTENANCE_UPDATED', 'Maintenance updated!');
$smarty->assign('PHOTO_UPLOADED', 'Photo uploaded!');
$smarty->assign('PHOTO_UPLOAD_FAILED', 'Photo upload failed.');
$smarty->assign('PHOTO_DELETED', 'Photo deleted!');
$smarty->assign('MAINTENANCE_NOT_FOUND', 'Maintenance record not found.');
$smarty->assign('ADMIN_FOOTER_TITLE', 'Admin Console');

// Admin configuration labels and hints
$smarty->assign('ADMIN_COLUMNS_TITLE', 'Columns to Show');
$smarty->assign('ADMIN_COLUMNS_HINT_TITLE', 'Number of columns visible in lists and dashboards.');
$smarty->assign('ADMIN_PRIMARY_TITLE', 'Primary Unit Label');
$smarty->assign('ADMIN_PRIMARY_HINT_TITLE', 'Label used for the primary unit identifier (for example: Asset ID).');
$smarty->assign('ADMIN_SECONDARY_TITLE', 'Secondary Unit Label');
$smarty->assign('ADMIN_SECONDARY_HINT_TITLE', 'Label used for the secondary unit identifier (for example: Serial Number).');

// Admin configuration page title
$smarty->assign('ADMIN_CONFIGURATION_TITLE', 'Configuration');

// Admin quick-add user
$smarty->assign('ADMIN_QUICK_ADD_USER_TITLE', 'Quick Add User');
$smarty->assign('ADMIN_ADD_USER_TITLE', 'Add User');

// Common field labels
$smarty->assign('EMAIL_TITLE', 'Email');

// System announcement label shown on login
$smarty->assign('SYSTEM_ANNOUNCEMENT_LABEL', 'System Announcement');

// ----- Admin: Users -----
$smarty->assign('PRIVILEGE_USER', 'User');
$smarty->assign('PRIVILEGE_ADMIN', 'Admin');
$smarty->assign('PASSWORD_PLACEHOLDER_NEW', 'New password (optional)');
$smarty->assign('ADMIN_DELETE_CONFIRM_USER', 'Are you sure you want to delete this user? This action cannot be undone.');

// ----- Units / General -----
$smarty->assign('UNITS_TITLE', 'Units');
$smarty->assign('VIEW_MAINTENANCE_BUTTON', 'View Maintenance');
$smarty->assign('ARCHIVE_BUTTON', 'Archive');
$smarty->assign('EDIT_BUTTON', 'Edit');
$smarty->assign('SEARCH_ACTIVE_PLACEHOLDER', 'Search active units...');

$smarty->assign('UNKNOWN_LABEL', 'Unknown');

// ----- User page strings -----
$smarty->assign('USER_INFO_TITLE', 'User Info');
$smarty->assign('NICKNAME_LABEL', 'Nickname');
$smarty->assign('NICKNAME_HINT', 'Enter your nickname here, will be displayed on your maintenance records');
$smarty->assign('API_KEY_TITLE', 'API Key');
$smarty->assign('API_KEY_HINT', 'Keep your API key secret. Generating a new key will invalidate the old one.');
$smarty->assign('BTN_GENERATE_KEY', 'Generate New');
$smarty->assign('CURRENT_PASSWORD_LABEL', 'Current Password');
$smarty->assign('CURRENT_PASSWORD_HINT', 'Enter your current password to change your password.');
$smarty->assign('NEW_PASSWORD_LABEL', 'New Password');
$smarty->assign('CONFIRM_NEW_PASSWORD_LABEL', 'Confirm New Password');

// Units UI extras
$smarty->assign('BTN_ADD', 'Add');
$smarty->assign('DETAILS_TITLE', 'Details');
$smarty->assign('BTN_SUBMIT', $smarty->getTemplateVars('BTN_SUBMIT'));
$smarty->assign('ACTIVE_LABEL', 'Active');
$smarty->assign('SEARCH_ARCHIVED_PLACEHOLDER', 'Search archived units...');
$smarty->assign('ARCHIVED_LABEL', 'Archived');
$smarty->assign('UNARCHIVE_BUTTON', 'Unarchive');

// ----- Questions (manage units) -----
$smarty->assign('QUESTIONS_TITLE', 'Questions');
$smarty->assign('QUESTION_LABEL_PLACEHOLDER', 'Question label');
$smarty->assign('TYPE_SHORT_TEXT', 'Short Text');
$smarty->assign('TYPE_LONG_TEXT', 'Long Text');
$smarty->assign('TYPE_NUMBER', 'Number');
$smarty->assign('TYPE_DATE', 'Date');
$smarty->assign('TYPE_MULTI_CHOICE', 'Multi-Choice');
$smarty->assign('CHOICES_PLACEHOLDER', 'Choices (comma separated)');
$smarty->assign('ORDER_PLACEHOLDER', 'Order');

// Buttons & actions specific to questions
$smarty->assign('BTN_ADD_QUESTION', 'Add Question');
$smarty->assign('BTN_DELETE_QUESTION', $smarty->getTemplateVars('BTN_DELETE'));
$smarty->assign('CONFIRM_DELETE_QUESTION', 'Delete this question?');

// Move actions
$smarty->assign('ACTION_MOVE_UP', '↑');
$smarty->assign('ACTION_MOVE_DOWN', '↓');

// Table headers
$smarty->assign('TH_LABEL', 'Label');
$smarty->assign('TH_TYPE', 'Type');
$smarty->assign('TH_OPTIONS', 'Options');
$smarty->assign('TH_ORDER', 'Order');
$smarty->assign('TH_ACTIONS', 'Actions');

// ----- Footer / Misc -----
$smarty->assign('MESSAGE_BOARD_TITLE', 'Message Board');
$smarty->assign('DASHBOARD_TITLE', 'Dashboard');
$smarty->assign('LATEST_MAINTENANCE_TITLE', 'Latest Maintenance');

// ----- Maintenance page strings -----
$smarty->assign('MAINTENANCE_FOR_TITLE', 'Maintenance for');
$smarty->assign('BACK_TO_LABEL', 'Back to');
$smarty->assign('MAINTENANCE_ADD_RECORD_TITLE', 'Add Maintenance Record');
$smarty->assign('MAINTENANCE_SERVICE_DETAILS', 'Service Details');
$smarty->assign('TYPE_OF_SERVICE_LABEL', 'Type of Service');
$smarty->assign('TYPE_OF_SERVICE_PLACEHOLDER', 'e.g. Inspection, Repair');
$smarty->assign('PERFORMED_BY_LABEL', 'Performed By');
$smarty->assign('PERFORMED_ON_LABEL', 'Performed On');
$smarty->assign('COSTS_OF_PARTS_LABEL', 'Costs of Parts');
$smarty->assign('DESCRIPTION_LABEL', 'Description');
$smarty->assign('DESCRIPTION_PLACEHOLDER', 'Describe the maintenance performed...');
$smarty->assign('PHOTOS_TITLE', 'Photos');
$smarty->assign('UPLOAD_PHOTOS_LABEL', 'Upload Photos');
$smarty->assign('PHOTOS_HINT', 'You can select multiple images.');
$smarty->assign('MAINTENANCE_RECORDS_TITLE', 'Maintenance Records');
$smarty->assign('SEARCH_MAINTENANCE_PLACEHOLDER', 'Search maintenance records...');
$smarty->assign('TH_ID', 'ID');
$smarty->assign('TH_TYPE', 'Type');
$smarty->assign('TH_DESCRIPTION', 'Description');
$smarty->assign('BTN_VIEW', 'View');

$smarty->assign('NO_MAINTENANCE_RECORDS', 'No maintenance records found.');

$smarty->assign('MAINTENANCE_DETAILS_TITLE', 'Maintenance Details');
$smarty->assign('BTN_UPDATE_MAINTENANCE', 'Update Maintenance');
$smarty->assign('BTN_ADD_PHOTO', 'Add Photo');

// Legacy keys kept for compatibility (aliases)
$smarty->assign('ADD_QUESTION_BUTTON', $smarty->getTemplateVars('BTN_ADD_QUESTION'));
$smarty->assign('MOVE_UP', $smarty->getTemplateVars('ACTION_MOVE_UP'));
$smarty->assign('MOVE_DOWN', $smarty->getTemplateVars('ACTION_MOVE_DOWN'));

// End of language file

