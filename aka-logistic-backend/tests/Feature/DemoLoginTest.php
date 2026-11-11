<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    public function test_demo_login_button_is_disabled_by_default(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('Try Demo Account');
    }

    public function test_demo_login_button_can_be_enabled_in_testing(): void
    {
        config(['app.demo_login_enabled' => true]);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Try Demo Account');
    }

    public function test_demo_login_button_stays_disabled_in_production(): void
    {
        config(['app.demo_login_enabled' => true]);
        $this->app['env'] = 'production';

        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('Try Demo Account');
    }
}
