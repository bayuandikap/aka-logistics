<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        if (app()->environment(['local', 'testing'])) {
            $actions[] = Action::make('demoLogin')
                ->label('Try Demo Account')
                ->color('gray')
                ->action(fn () => $this->authenticateDemoAccount());
        }

        return $actions;
    }

    public function authenticateDemoAccount(): ?LoginResponse
    {
        abort_unless(app()->environment(['local', 'testing']), 404);

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = User::query()
            ->where('email', 'admin@aka-logistics.test')
            ->first();

        if (
            ! $user ||
            ! $user->hasRole('super_admin') ||
            ! $user->canAccessPanel(Filament::getCurrentPanel())
        ) {
            Notification::make()
                ->title('Demo account is unavailable')
                ->body('Run the database seeders in a local or testing environment.')
                ->danger()
                ->send();

            return null;
        }

        Filament::auth()->login($user);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}
