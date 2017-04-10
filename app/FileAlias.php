<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * App\FileAlias.
 *
 * @property int $id
 * @property int $proxy_file_id
 * @property string $path
 * @property int $hits_left
 * @property \Carbon\Carbon $valid_from
 * @property \Carbon\Carbon $valid_until
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $hits_total
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AliasHit[] $hits
 * @property-read \App\ProxyFile $proxyFile
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereHitsLeft($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereProxyFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereValidFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FileAlias whereValidUntil($value)
 * @mixin \Eloquent
 */
class FileAlias extends Model
{
    protected $fillable = [
        'proxy_file_id',
        'path',
        'hits_left',
        'valid_from',
        'valid_until',
    ];

    protected $dates = [
        'valid_from',
        'valid_until',
    ];

    public function proxyFile()
    {
        return $this->belongsTo(ProxyFile::class);
    }

    public function hits()
    {
        return $this->hasMany(AliasHit::class);
    }

    public static function byPath(string $path): self
    {
        return static::wherePath($path)->firstOrFail();
    }

    /**
     * finds an alias by combined key.
     *
     * @param string $combinedKey
     * @return FileAlias
     * @throws ModelNotFoundException when combined key does not fetch a valid alias
     */
    public static function byCombinedKey(string $combinedKey): self
    {
        list($reference, $aliasId) = explode('.', $combinedKey);

        $proxyFile = ProxyFile::byReference($reference);

        return $proxyFile->aliases()->where('id', $aliasId)->firstOrFail();
    }

    /**
     * do we have any hits left.
     *
     * @return bool
     */
    public function hitsLeft(): bool
    {
        return ($this->hits_left === null)
            ? true
            : $this->hits_left > 0;
    }

    /**
     * is the file alias valid now.
     *
     * @return bool
     */
    public function isValidNow(): bool
    {
        /** @var \Carbon\Carbon $now */
        $now = Carbon::now();

        return $this->valid_from->lessThanOrEqualTo($now)
            && ($this->valid_until === null
                || $this->valid_until->greaterThanOrEqualTo($now));
    }

    /**
     * tracks a hit.
     *
     * @param string|null $userAgent
     *
     * @return \App\AliasHit
     */
    public function trackHit(string $userAgent = null): AliasHit
    {
        if ($this->hits_left !== null) {
            $this->hits_left--;
            $this->save();
        }

        return $this->hits()->create([
            'user_agent' => $userAgent,
        ]);
    }

    public function getHitsTotalAttribute(): int
    {
        if ($this->hits_left === null) {
            return -1;
        }

        return $this->hits()->count() + $this->hits_left;
    }
}
