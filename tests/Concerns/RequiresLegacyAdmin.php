<?php

namespace Tests\Concerns;

trait RequiresLegacyAdmin
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! config('features.legacy_admin')) {
            $this->markTestSkipped('Legacy admin routes are disabled. Set FEATURE_LEGACY_ADMIN=true to run this test.');
        }
    }
}
