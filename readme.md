# File Proxy Application

A Laravel-based file proxy application. Ready to go.

[![Build Status](https://travis-ci.org/ipunkt/fileproxy.svg?branch=master)](https://travis-ci.org/ipunkt/fileproxy)

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

For building assets you need node/yarn.

```bash
yarn
```

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

### Local Development

We (ipunkt) provide a package called rancherize for hosting our web stacks in a rancher environment with various docker images. For local development you can use rancherize command in the following way.

You need locally docker daemon installed.

Copy following content in a `rancherize.json` file in your project root:
```json
{
    "blueprints": {
        "webserver": "Rancherize\\Blueprint\\Webserver\\WebserverBlueprint"
    },
    "blueprint": "webserver",
    "default": {
        "rancher": {
            "account": "ipunkt"
        },
        "docker": {
            "account": "default",
            "repository": "repo\/name",
            "version-prefix": "",
            "base-image": "busybox"
        },
        "nginx-config": "",
        "service-name": "Fileproxy"
    },
    "environments": {
        "local": {
            "debug-image": true,
            "sync-user-into-container": true,
            "expose-port": 18129,
            "use-app-container": false,
            "mount-workdir": true,
            "add-redis": false,
            "add-database": true,
            "database": {
                "pma": {
                    "enable": true,
                    "require-login": false,
                    "expose": true
                },
                "pma-port": 9755
            },
            "php": "7.0",
            "environment": {
                "APP_ENV": "local",
                "APP_DEBUG": true,
                "APP_KEY": "base64:jid3KQRva+vTtpU2mK6hvWxrLI6vmOmIwn\/AEAH4ua0="
            }
        }
    }
}
```

Then hit `vendor/bin/rancherize start local` and wait until the command line shows you the following output:
```bash
Service PMA was exposed to the ports 9755
Link for convenience: http://localhost:9755
Service Fileproxy was exposed to the ports 18129
Link for convenience: http://localhost:18129
```
We provide a local [phpMyAdmin](http://localhost:9755) and the [web application frontend](http://localhost:18129). 

## Assets

Build all assets with the command
```bash
yarn run dev
```
or
```bash
yarn run prod
```
for production output.

We ship already-built assets in this repository. So you do not need to build your own.
