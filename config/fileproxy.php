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
];
