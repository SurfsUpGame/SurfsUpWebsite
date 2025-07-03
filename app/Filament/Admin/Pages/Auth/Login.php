<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();
    }

    public function form(Form $form): Form
    {
        return parent::form($form);
    }

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            $this->getSteamLoginAction(),
        ]);
    }

    protected function getSteamLoginAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('steam_login')
            ->label('Login with Steam')
            ->color('gray')
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->url(route('auth.steam'))
            ->extraAttributes([
                'class' => 'w-full',
            ]);
    }

    public function getHeading(): string|Htmlable
    {
        return 'Sign in to your account';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Or use your Steam account';
    }
}
