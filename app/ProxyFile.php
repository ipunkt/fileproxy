<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProxyFile.
 *
 * @property int $id
 * @property string $reference
 * @property string $type
 * @property string $filename
 * @property string $mimetype
 * @property int $size
 * @property string $checksum
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\FileAlias[] $aliases
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AliasHit[] $hits
 * @property-read \App\LocalFile $localFile
 * @property-read \App\RemoteFile $remoteFile
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereChecksum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereFilename($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereMimetype($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereReference($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProxyFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProxyFile extends Model
{
    protected $fillable = [
        'reference',
        'type',
        'filename',
        'mimetype',
        'size',
        'checksum',
    ];

    public function localFile()
    {
        return $this->hasOne(LocalFile::class);
    }

    public function remoteFile()
    {
        return $this->hasOne(RemoteFile::class);
    }

    public function file()
    {
        if ($this->type === 'local') {
            return $this->localFile();
        }

        return $this->remoteFile();
    }

    public function aliases()
    {
        return $this->hasMany(FileAlias::class);
    }

    public function hits()
    {
        return $this->hasManyThrough(AliasHit::class, FileAlias::class);
    }

    /**
     * @param string $reference
     *
     * @return \App\ProxyFile
     */
    public static function byReference(string $reference): self
    {
        return static::whereReference($reference)->firstOrFail();
    }
}
