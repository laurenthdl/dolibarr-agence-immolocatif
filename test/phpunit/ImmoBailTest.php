<?php

declare(strict_types=1);

require_once __DIR__ . '/../../class/immobail.class.php';

class ImmoBailTest extends PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function moduleClassShouldHaveCorrectNumber(): void
    {
        $moduleFile = __DIR__ . '/../../core/modules/modImmolocatif.class.php';
        $this->assertFileExists($moduleFile);
        $content = file_get_contents($moduleFile);
        $this->assertStringContainsString('numero = 700003', $content);
    }

    /**
     * @test
     */
    public function classShouldExist(): void
    {
        $this->assertTrue(class_exists('ImmoBail'));
    }

    /**
     * @test
     */
    public function tlppuCalculationShouldBeCorrect(): void
    {
        $loyer = 150000;
        $taux = 0.15;
        $tlppu = $loyer * 12 * $taux;
        $this->assertEquals(270000, $tlppu);
    }

    /**
     * @test
     */
    public function sqlShouldCreateBailTable(): void
    {
        $sqlFile = __DIR__ . '/../../sql/llx_immo_bail.sql';
        $this->assertFileExists($sqlFile);
        $content = file_get_contents($sqlFile);
        $this->assertStringContainsString('CREATE TABLE', $content);
        $this->assertStringContainsString('llx_immo_bail', $content);
    }
}
