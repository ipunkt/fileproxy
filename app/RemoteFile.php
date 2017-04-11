<?php

namespace App;

use App\Traits\FileSeparationConcern;
use Illuminate\Database\Eloquent\Model;

/**
 * App\RemoteFile.
 *
 * @property int $id
 * @property int $proxy_file_id
 * @property string $url
 * @property array $options
 * @property string $path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\ProxyFile $proxyFile
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereOptions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereProxyFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\RemoteFile whereUrl($value)
 * @mixin \Eloquent
 */
class RemoteFile extends Model
{
    use FileSeparationConcern;

    protected $fillable = [
        'proxy_file_id',
        'url',
        'path',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function proxyFile()
    {
        return $this->belongsTo(ProxyFile::class);
    }

    public function getLocalStoragePath(): string
    {
        return 'remote/' . $this->getPathSeparated($this->getKey());
    }
}
