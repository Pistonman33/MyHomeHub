<?php

namespace Tests\Feature;

use App\Console\Commands\UpdateAfttPassword;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

class CttAfttPasswordCommandTest extends TestCase
{
    public function test_password_reset_requires_an_interactive_terminal(): void
    {
        $command = new UpdateAfttPassword();
        $command->setLaravel($this->app);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute(
            ['license' => 167814],
            ['interactive' => false]
        );

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString(
            'This command requires an interactive terminal so the password can be entered without echoing it.',
            $tester->getDisplay()
        );
    }
}