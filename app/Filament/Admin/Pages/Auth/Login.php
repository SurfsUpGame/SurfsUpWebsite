<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Actions\Action;
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

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSteamLoginAction(),
        ];
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
        return 'Sign in with your Steam account';
    }
}
