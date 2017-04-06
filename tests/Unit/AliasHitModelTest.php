<?php

namespace Tests\Unit;

use App\AliasHit;
use App\FileAlias;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AliasHitModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_an_alias_hit_model()
    {
        // ARRANGE
        /** @var FileAlias $fileAlias */
        $fileAlias = factory(FileAlias::class, 'full')->create();

        // ACT
        /** @var AliasHit $aliasHit */
        $aliasHit = factory(AliasHit::class)->make([
            'file_alias_id' => $fileAlias->getKey(),
        ]);

        // ASSERT
        $this->assertTrue($aliasHit->save());
    }
}
