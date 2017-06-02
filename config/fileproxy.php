<?php

return [
    /*
     * automatically cache remote files locally
     */
    'cache_remote_files' => env('FILEPROXY_CACHE_REMOTE_FILES', false),

    /*
     * runtime mode of fileproxy application
     * valid options are:
     * - default (UI + API)
     * - ui
     * - api
     */
    'mode' => env('FILEPROXY_APPLICATION_MODE', 'default'),

    /*
     * web ui configuration
     */
    'web' => [

        /*
         * accept file upload in web ui
         * -> everybody can upload files
         */
        'accept_file_upload' => env('FILEPROXY_WEB_ACCEPT_FILE_UPLOAD', false),

        /*
         * accept remote file creation in web ui
         * -> everybody can create remote files
         */
        'accept_remote_creation' => env('FILEPROXY_WEB_ACCEPT_REMOTE_CREATION', false),
    ],

    /*
     * api configuration
     */
    'api' => [
        /*
         * The secret token has to be set for security reason. If it is not null you have to
         * add a http header `X-FILEPROXY-TOKEN` to each api request.
         */
        'secret_token' => env('FILEPROXY_API_SECRET_TOKEN', null),

        /*
         * The secret token has to be set for security reason. If it is not null you have to
         * add a http header `X-FILEPROXY-TOKEN` to each api request.
         */
        'token_name' => env('FILEPROXY_API_TOKEN_NAME', 'X-FILEPROXY-TOKEN'),
    ],
];
