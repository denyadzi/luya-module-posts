<?php

return [

// added translation in 1.0.0-beta5:

    'posts' => 'Новости',
    'posts_administrate' => 'Управлять',

    //article
    'article' => 'Новость',
    'article_cat_id' => 'Категория',
    'article_title' => 'Заголовок',
    'article_text' => 'Описание',
    'article_image_id' => 'Изображение',
    'article_timestamp_create' => 'Дата',
    'article_timestamp_display_from' => 'Показывать с ',
    'article_timestamp_display_until' => 'Показывать до',
    'article_is_display_limit' => 'Лимит показов',
    'article_image_list' => 'Список изображений',
    'article_file_list' => 'Список файлов',
    'article_tag' => 'Тег',

    //cat
    'cat' => 'Категории',
    'cat_title' => 'Категория',
    'cat_delete_error' => 'Эта категория до сих пор используется в одной или нескольких галерей и не может быть удалена.',
    'cat_title_create_error' => 'Пожалуйста заполните название.',

    //tag
    'tag' => 'Теги',
    'tag_title' => 'Тег',
    'tag_title_create_error' => 'Заполните тег.',

    // 1.0.0

    'teaser_text' => 'Текст тизера',

// 2.1.0

    'article_autopost' => 'Автопубликация',
    'article_autopost_no_configs' => 'Не найдено сконфигурированных автопубликаций. Пожалуйста, создайте новую конфигурацию или не используйте функцию автопубликации сняв галочку Автопубликации',
    'article_autopost_config_empty_token: {id}' => 'Конфигурация автопубликаций с id {id} содержит пустой токен доступа. Для использования функции автопубликации необходимо обновить токен доступа в этой конфигурации автопубликации',
    'article_autopost_check_no_response' => 'Ошибка валидации токена сконфигурированной автопубликации. Произошла ошибка сетевого соединения',
    'article_autopost_check_error_response' => 'Ошибка валидации токена сконфигурированной автопубликации. Неверный запрос. Пожалуйста, обратитесь к разработчику',
    'article_autopost_check_invalid_token: {id}' => 'Конфигурация автопубликации с id {id} имеет невалидный (с истекшим сроком) токен. Для продолжения необходимо обновить токен или снять галочку Автопубликации',
    'autopost_config' => 'Конфигурации автопубликаций',
    'autopost_config_id' => 'ID',
    'autopost_config_type' => 'Тип',
    'autopost_config_access_token' => 'Токен доступа',
    'autopost_config_lang_id' => 'Язык',
    'autopost_config_with_link' => 'Публиковать со ссылкой',
    'autopost_config_with_message' => 'Публиковать с сообщением',
    'autopost_config_exception_extend_fb_token_no_response' => 'Не удалось продлить термин действия токена Facebook. Произошла ошибка сетевого соединения',
    'autopost_config_exception_extend_fb_token_fail' => 'Не удалось продлить термин действия токена Facebook. В ответе отсутствует токен',
    'autopost_config_exception_invalid_type_configuration: {type}' => 'Неверная конфигурация автопубликаций {type}. Пожалуйста, обратитесь к разработчику',
    'js_autopost_config_label_renew_token' => 'Обновить токен',
    'js_autopost_config_fb_login_fail' => 'Ошибка авторизации в Facebook. Пожалуйста, повторите попытку',
];
