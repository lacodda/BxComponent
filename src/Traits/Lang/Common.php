<?php

    namespace Lacodda\BxComponent\Traits\Lang;

    /**
     * Class Common
     *
     * @package Lacodda\BxComponent\Traits\Lang
     */
    class Common
    {
        /**
         * @var array
         */
        public $mess = [
            'BBC_COMPONENT_CATCH_EXCEPTION'         => 'Произошла ошибка. Уведомление администратору уже отправлено. Приносим свои извинения.',
            'BBC_COMPONENT_EXCEPTION_EMAIL_SUBJECT' => 'Выброшено исключение на сайте #SITE_URL#.',
            'BBC_COMPONENT_EXCEPTION_EMAIL_TEXT'    => 'Страница: #URL#<br />Дата: #DATE#<p style="color: red;
">#EXCEPTION_MESSAGE#</p><p>#EXCEPTION#</p>',
        ];
    }
