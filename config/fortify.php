<?php

return [

    'middleware' => ['web'],

    'auth_middleware' => 'auth',

    'home' => '/today',

    'limiters' => [
        'login' => 'login',
    ],

    'views' => true,

];
