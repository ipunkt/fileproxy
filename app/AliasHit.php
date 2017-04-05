<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AliasHit
 *
 * @property int $id
 * @property int $file_alias_id
 * @property string $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\AliasHit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AliasHit whereFileAliasId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AliasHit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AliasHit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AliasHit whereUserAgent($value)
 * @mixin \Eloquent
 */
class AliasHit extends Model
{
    protected $fillable = [
        'user_agent',
    ];
}
