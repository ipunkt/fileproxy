<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\ProxyFile::class, function (Faker\Generator $faker) {
    return [
        'reference' => $faker->uuid,
        'type' => 'local',
        'filename' => $faker->slug.'.'.$faker->fileExtension,
        'mimetype' => $faker->mimeType,
        'size' => $faker->numberBetween(),
        'checksum' => $faker->md5,
    ];
});

$factory->define(App\ProxyFile::class, function (Faker\Generator $faker) {
    return [
        'reference' => $faker->uuid,
        'type' => 'local',
        'filename' => $faker->slug.'.'.$faker->fileExtension,
        'mimetype' => $faker->mimeType,
        'size' => $faker->numberBetween(),
        'checksum' => $faker->md5,
    ];
}, 'local');

$factory->define(App\ProxyFile::class, function (Faker\Generator $faker) {
    return [
        'reference' => $faker->uuid,
        'type' => 'remote',
        'filename' => $faker->slug.'.'.$faker->fileExtension,
        'mimetype' => $faker->mimeType,
        'size' => $faker->numberBetween(),
        'checksum' => $faker->md5,
    ];
}, 'remote');

$factory->define(App\FileAlias::class, function (Faker\Generator $faker) {
    return [
        'path' => $faker->slug.'.'.$faker->fileExtension,
        'valid_from' => $faker->dateTime,
    ];
});

$factory->define(App\FileAlias::class, function (Faker\Generator $faker) {
    $proxyFile = factory(\App\ProxyFile::class)->create();

    return [
        'proxy_file_id' => $proxyFile->getKey(),
        'path' => $faker->slug.'.'.$faker->fileExtension,
        'valid_from' => $faker->dateTime,
    ];
}, 'full');

$factory->define(App\AliasHit::class, function (Faker\Generator $faker) {
    return [
        'user_agent' => $faker->userAgent,
    ];
});

$factory->define(App\AliasHit::class, function (Faker\Generator $faker) {
    $fileAlias = factory(\App\FileAlias::class)->create();

    return [
        'file_alias_id' => $fileAlias->getKey(),
        'user_agent' => $faker->userAgent,
    ];
}, 'full');

$factory->define(App\LocalFile::class, function (Faker\Generator $faker) {
    return [
        'path' => '/'.$faker->slug,
    ];
});

$factory->define(App\LocalFile::class, function (Faker\Generator $faker) {
    $proxyFile = factory(\App\ProxyFile::class)->create();

    return [
        'proxy_file_id' => $proxyFile->getKey(),
        'path' => '/'.$faker->slug,
    ];
}, 'full');

$factory->define(App\RemoteFile::class, function (Faker\Generator $faker) {
    return [
        'url' => $faker->url,
        'path' => '/'.$faker->slug,
    ];
});

$factory->define(App\RemoteFile::class, function (Faker\Generator $faker) {
    $proxyFile = factory(\App\ProxyFile::class)->create();

    return [
        'proxy_file_id' => $proxyFile->getKey(),
        'url' => $faker->url,
        'path' => '/'.$faker->slug,
    ];
}, 'full');
