<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppVersionManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     *@test
     * @return void
     */
    public function should_get_app_version()
    {
        $this->seedAppVersion();
        $response = $this->get('/api/v1/version');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'app versions retrived successfully'
        ]);
    }

    public function seedAppVersion()
    {
        DB::table('app_versions')->insert([
            'minor' => 1,
            'major' => 1,
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
        ]);
    }
}
