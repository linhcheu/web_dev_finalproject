<?php

namespace Tests\Unit;

use App\Support\ImageStore;
use PHPUnit\Framework\TestCase;

class ImageStoreTest extends TestCase
{
    public function test_upload_rule_only_allows_small_images(): void
    {
        $this->assertStringContainsString('image', ImageStore::RULE);
        $this->assertStringContainsString('max:2048', ImageStore::RULE);
    }
}
