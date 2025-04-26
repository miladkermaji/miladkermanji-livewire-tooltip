<?php

namespace Miladkermanji\LivewireTooltip;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireTooltipServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register Livewire component
        Livewire::component('miladkermanji-tooltip', Tooltip::class);

        // Publish configuration
        $this->publishes([
            __DIR__ . '/config/livewire-tooltip.php' => config_path('livewire-tooltip.php'),
        ], 'livewire-tooltip-config');
    }

    public function register()
    {
        // Merge configuration
        $this->mergeConfigFrom(__DIR__ . '/config/livewire-tooltip.php', 'livewire-tooltip');
    }
}