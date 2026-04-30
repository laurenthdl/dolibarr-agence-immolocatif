<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../../core/modules/modImmolocatif.class.php';

class ImmoLocatifTest extends PHPUnit\Framework\TestCase
{
    /** @test */
    public function moduleShouldExist(): void
    {
        $this->assertTrue(class_exists('modImmolocatif'));
    }

    /** @test */
    public function uiFilesShouldExist(): void
    {
        $this->assertFileExists(__DIR__ . '/../../index.php');
        $this->assertFileExists(__DIR__ . '/../../card.php');
    }
}
