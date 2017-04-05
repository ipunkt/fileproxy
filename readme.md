# File Proxy Application

A Laravel-based file proxy application. Ready to go.


## Installation

You can install by composer
```bash
composer create-project --prefer-dist ipunkt/fileproxy your-file-proxy-app
```

or simply download from [github](https://github.io/ipunkt/fileproxy).

After installing files locally you should configure your application. We have several running modes and conditional behaviour, so read out configuration chapter carefully.

## Recommendations

We recommend running a queue - not in sync. Especially for remote file support it can be a headache when you do remote file fetching in sync.


## Configuration

The whole application is powered by configuration values in `config/fileproxy.php`. The following section clears the meaning of each value.

### Settings

#### cache_remote_files

It is possible to cache all remote files. This helps you playing a real proxy that caches all served files. If you do not cache any remote files locally than file proxy will pass through the remote url.

We recommend caching of remote files.

**Default**: `false`

**Environment variable**: `FILEPROXY_CACHE_REMOTE_FILES`


### Web

All settings depending on the web frontend.

#### accept_file_upload

You can enable or disable the ability to upload files via web frontend. This allows everybody to upload files to your file proxy application.

**Default**: `false`

**Environment variable**: `FILEPROXY_WEB_ACCEPT_FILE_UPLOAD`

#### accept_remote_creation

You can enable or disable the ability to create remote files via web frontend. This allows everybody to create remote files to your file proxy application.

**Default**: `false`

**Environment variable**: `FILEPROXY_WEB_ACCEPT_REMOTE_CREATION`

## Test

All commands for running the business functions are unit tested. You can run `composer test` to run our test suite.
