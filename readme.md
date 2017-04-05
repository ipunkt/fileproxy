# File Proxy Application

A Laravel-based file proxy application. Ready to go.

## Purpose

File Proxy provides file sharing with a bit more management. You can share files with one-time links, x-time links, time-based links and so on.

Every download gets checked and access statistics will be stored.

You can upload files for sharing or you can provide an url to be shared.

For every file you want to share you can create several aliases with an access policy.

For example:

You have file `pricelist.pdf` and you want to share with your clients. But every client should get his own, so you can check the download history for each. So you create one alias for the `pricelist.pdf` for each of your clients: `client1-pricelist.pdf`, `client2-pricelist-2017.pdf` and so on. Every alias can be configured individually - in the example maybe a time-limited and hit-limited download. So each client can only download this file 10 times until the end of this year.


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
