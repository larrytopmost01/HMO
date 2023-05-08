<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\PromoCodeGenerator as PromoCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromoCodeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *@test
     * @return void
     */
    public function should_genrate_promo_code()
    {
        $promo_code = PromoCode::generatePromoCode();
        $this->assertEquals(9, strlen($promo_code));
    }

    public function seedPromoCode()
    {
        DB::table('promos')->insert([
            [
                'user_id' => 1,
                'code' => 'A01B23C45',
                'discount_percent' => 10,
                'dicounted_amount_ngn' => 1000,
                'amount_paid_ngn' => 2000,
                'cos_ngn' => 3000,
                'service_name' => 'dental',
                'is_used' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]]);
    }
}
