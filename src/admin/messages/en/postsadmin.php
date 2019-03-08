<?php

return [

// added translation in 1.0.0-beta5:

    'posts' => 'Posts',
    'posts_administrate' => 'Administrate',
    'article' => 'Post entry',
    'article_cat_id' => 'Category',
    'article_title' => 'Title',
    'article_text' => 'Content',
    'article_image_id' => 'Image',
    'article_timestamp_create' => 'Date',
    'article_timestamp_display_from' => 'Display from ',
    'article_timestamp_display_until' => 'Display until',
    'article_is_display_limit' => 'Limit display',
    'article_image_list' => 'Image list',
    'article_file_list' => 'File list',
    'article_tag' => 'Tag',
    'cat' => 'Categories',
    'cat_title' => 'Category',
    'cat_delete_error' => 'This category is used by one or multiple posts and can not be deleted.',
    'cat_title_create_error' => 'Please add a category title.',
    'tag' => 'Tags',
    'tag_title' => 'Tag',
    'tag_title_create_error' => 'Please add a tag title.',
    
// 1.0.0
    
    'teaser_text' => 'Teaser Text',

// 2.1.0

    'article_autopost' => 'Autopost',
    'article_autopost_no_configs' => 'No autopost configuration found. Please, create one or don\'t use the autopost function by unsetting the autopost flag',
    'article_autopost_check_no_response' => 'Failed to validate autopost config token. The network connection problem occurred',
    'article_autopost_check_error_response' => 'Failed to validate autopost config token. Bad request. Please, contact the developer',
    'article_autopost_check_invalid_token: {id}' => 'Autopost configuration with id {id} has an invalid (expired) token. You must renew token or unset the autopost flag to proceed',
    'autopost_config' => 'Autopost config',
    'autopost_config_id' => 'ID',
    'autopost_config_type' => 'Type',
    'autopost_config_access_token' => 'Access token',
    'autopost_config_lang_id' => 'Language',
    'autopost_config_with_link' => 'Post with link',
    'autopost_config_with_message' => 'Post with message',
    'autopost_config_owner_id' => 'Owner ID',
    'autopost_config_exception_extend_fb_token_no_response' => 'Failed to extend facebook token. Network connection problem occurred',
    'autopost_config_exception_extend_fb_token_fail' => 'Failed to extend facebook token. No token in reponse',
    'autopost_config_exception_invalid_type_configuration: {type}' => 'Invalid {type} autopost configuration. Please, contact the developer',
    'js_autopost_config_label_renew_token' => 'Renew token',
    'js_autopost_config_fb_login_fail' => 'Facebook login failed. Please, try again',
    'autopost_queue_job' => 'Autopost Queue Jobs',
    'autopost_queue_job_id' => 'ID',
    'autopost_queue_job_data' => 'Job Data',
    'autopost_queue_job_timestamp_finish' => 'Done',
    'autopost_queue_job_timestamp_reserve' => 'Reserved',
    'autopost_queue_job_timestamp_create' => 'Created',
    'autopost_queue_job_timestamp_update' => 'Updated',
];
