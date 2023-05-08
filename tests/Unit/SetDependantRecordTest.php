<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Enrollee;
use App\Utils\SetDependantRecord;
use App\Utils\Proxy;

class SetDependantRecordTest extends TestCase
{
    private $code = "GDM/21/303/B1";
    /**
     * @test
     * @return void
     */
    public function should_get_unique_code_for_dependant()
    {
        $record = new SetDependantRecord($this->code);
         //input: "GDM/21/303/B1"  Output: "GDM/21/303"
        $this->assertEquals("GDM/21/303", $record->unique_code);
    }

    /**
     * @test
     * @return void
     */
    public function should_get_unique_code_for_principal()
    {
        $record = new SetDependantRecord("GDM/21/303/A");
         //input: "GDM/21/303/A"  Output: "GDM/21/303"
        $this->assertEquals("GDM/21/303", $record->unique_code);
    }

    /**
     * @test
     * @return void
     */
    public function should_get_principal_code()
    {
        $record = new SetDependantRecord($this->code);
        $this->assertEquals("GDM/21/303/A", $record->getPrincipalCode() . "/A");
        $this->assertNotNull($record->missing_records);
        $this->assertEquals(1, count($record->missing_records));
        $this->assertNotEmpty($record->missing_records["hospital"]);
        $this->assertNotNull($record->missing_records["hospital"]);
    }

    /**
     * @test
     * @return void
     */
    public function should_get_missing_records()
    {
        $record = new SetDependantRecord($this->code);
        $this->assertNotNull($record->getDependantMissingRecords());
        $this->assertIsString($record->getDependantMissingRecords());
    }

        /**
     * @test
     * @return void
     */
    public function should_return_null_for_invalid_enrollee_code()
    {
        $record = new SetDependantRecord("ABC/11/202/B1");
        $this->assertNull($record->getPrincipalCode());
    }
}

