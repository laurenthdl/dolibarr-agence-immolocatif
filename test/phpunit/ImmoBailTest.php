<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../../class/immobail.class.php';

class ImmoBailTest extends PHPUnit\Framework\TestCase
{
    /** @test */
    public function bailClassShouldExist(): void
    {
        $this->assertTrue(class_exists('ImmoBail'));
    }

    /** @test */
    public function quittanceClassShouldExist(): void
    {
        $this->assertTrue(class_exists('ImmoQuittance'));
    }

    /** @test */
    public function tlppuCalculationShouldBeCorrect(): void
    {
        $bail = new ImmoBail(new DoliDB());
        $bail->loyer_nu = 150000;
        $bail->taux_tlppu = 15;
        $tlppu = $bail->calculTLPPU();
        $this->assertEquals(22500.0, $tlppu);
    }

    /** @test */
    public function sqlShouldCreateBailTable(): void
    {
        $content = file_get_contents(__DIR__ . '/../../sql/llx_immo_bail.sql');
        $this->assertStringContainsString('CREATE TABLE', $content);
        $this->assertStringContainsString('llx_immo_bail', $content);
        $this->assertStringContainsString('llx_immo_quittance', $content);
    }

    /** @test */
    public function uiFilesShouldExist(): void
    {
        $this->assertFileExists(__DIR__ . '/../../index.php');
        $this->assertFileExists(__DIR__ . '/../../card.php');
    }
}
