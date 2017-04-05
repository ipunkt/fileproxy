<?php

namespace App;

use App\Traits\FileSeparationConcern;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LocalFile
 *
 * @property int $id
 * @property int $proxy_file_id
 * @property string $path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\ProxyFile $proxyFile
 * @method static \Illuminate\Database\Query\Builder|\App\LocalFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocalFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocalFile wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocalFile whereProxyFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LocalFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LocalFile extends Model
{
    use FileSeparationConcern;

    protected $fillable = [
        'proxy_file_id',
        'path',
    ];

    public function proxyFile()
    {
        return $this->belongsTo(ProxyFile::class);
    }

    public function getLocalStoragePath(): string
    {
        return 'local/' . $this->getPathSeparated($this->getKey());
    }
}
