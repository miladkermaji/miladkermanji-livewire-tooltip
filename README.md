Miladkermanji Livewire Tooltip
A lightweight Livewire component for displaying tooltips in Laravel applications using Popper.js. Supports static tooltips via data-tooltip and dynamic content via tooltip-method.
Features

Static tooltips with data-tooltip.
Dynamic tooltips with tooltip-method for Livewire method calls.
Smooth 300ms fade-in/fade-out animations.
Full compatibility with Livewire updates.
Prevents empty tooltips.
Configurable placement and animation duration.
Lightweight and publishable.

Requirements

PHP 8.1+
Laravel 10.0+ or 11.0+
Livewire 3.0+
Popper.js (via CDN or npm)

Installation

Install the package via Composer:
composer require miladkermanji/livewire-tooltip


Include Popper.js in your layout (e.g., resources/views/layouts/app.blade.php):
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>


Add the tooltip component to your layout or view:
<livewire:miladkermanji-tooltip wire:key="tooltip-component" />


(Optional) Publish the configuration file:
php artisan vendor:publish --tag=livewire-tooltip-config



Usage
Static Tooltip
Add the tooltip-link class and data-tooltip attribute to an element:
<button class="tooltip-link" data-tooltip="Tooltip text" data-placement="top">Hover me</button>

Dynamic Tooltip
Use tooltip-method to call a Livewire method:
<button class="tooltip-link" tooltip-method="\App\Livewire\MyComponent@myMethod" data-param1="value">Dynamic</button>

In your Livewire component:
public function myMethod($param1)
{
    return "Dynamic content for $param1";
}

Configuration
Edit config/livewire-tooltip.php to customize:
return [
    'default_placement' => 'top', // top, right, bottom, left
    'animation_duration' => 300,  // milliseconds
];

Example
In resources/views/livewire/dr/panel/turn/schedule/appointments-list.blade.php:
<button class="tooltip-link btn btn-light rounded-circle"
        data-tooltip="Cancel appointment"
        data-placement="top"
        wire:click="cancelSingleAppointment(1)">
    <img src="{{ asset('dr-assets/icons/cancle-appointment.svg') }}" alt="Cancel">
</button>

Publishing the Package

Create a GitHub repository (e.g., miladkermanji/livewire-tooltip).
Push the package code to the repository.
Submit to Packagist (https://packagist.org).
Tag a release:git tag v1.0.0
git push origin v1.0.0



Contributing
Contributions are welcome! Please submit a pull request or open an issue on the GitHub repository.
License
This package is open-sourced under the MIT License.
"# miladkermanji-livewire-tooltip" 
"# miladkermanji-livewire-tooltip" 
