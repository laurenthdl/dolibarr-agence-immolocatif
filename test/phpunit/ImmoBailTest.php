<?php

declare(strict_types=1);

require_once __DIR__ . '/../../class/immobail.class.php';

class ImmoBailTest extends PHPUnit\Framework\TestCase
{
    protected $object;

    protected function setUp(): void
    {
        global $db;
        $this->object = new ImmoBail($db);
    }

    /**
     * @test
     */
    public function tableElementShouldBeCorrect(): void
    {
        $this->assertEquals('llx_immo_bail', $this->object->table_element);
    }

    /**
     * @test
     */
    public function elementShouldBeCorrect(): void
    {
        $this->assertEquals('immolocatif', $this->object->element);
    }

    /**
     * @test
     */
    public function objectShouldHaveRefProperty(): void
    {
        $this->assertObjectHasProperty('ref', $this->object);
    }

    /**
     * @test
     */
    public function objectShouldHaveStatusProperty(): void
    {
        $this->assertObjectHasProperty('status', $this->object);
    }

    /**
     * @test
     */
    public function getNextNumRefShouldReturnFormattedString(): void
    {
        $ref = $this->object->getNextNumRef();
        $this->assertStringStartsWith(strtoupper($this->object->element), $ref);
        $this->assertMatchesRegularExpression('/^' . strtoupper($this->object->element) . '-\d{4}-\d{4}$/', $ref);
    }
}
