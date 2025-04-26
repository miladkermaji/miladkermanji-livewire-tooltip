# Livewire Tooltip

A lightweight Laravel package that provides a Livewire component for displaying tooltips using Popper.js. It supports both static tooltips via `data-tooltip` and dynamic content via `tooltip-method`, with smooth fade-in/fade-out animations.

---

## Features

- **Static Tooltips**: Display simple text tooltips using `data-tooltip`.
- **Dynamic Tooltips**: Fetch content from Livewire methods using `tooltip-method`.
- **Smooth Animations**: 300ms fade-in/fade-out transitions.
- **Livewire Compatible**: Works seamlessly with Livewire updates.
- **Prevent Empty Tooltips**: Ensures only valid tooltips are shown.
- **Customizable**: Configure placement and animation duration.
- **Lightweight**: Minimal footprint with no unnecessary dependencies.

---

## Requirements

- PHP 8.1 or higher
- Laravel 10.0, 11.0, or 12.0
- Livewire 3.0 or higher
- Popper.js (via CDN or npm)

---

## Installation

1. **Install the Package**

   Install the package via Composer:

   ```bash
   composer require miladkermanji/livewire-tooltip
   ```

2. **Add Popper.js**

   Include Popper.js in your Laravel layout (e.g., `resources/views/layouts/app.blade.php`) before the closing `</body>` tag:

   ```html
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
   ```

   Alternatively, if using npm:

   ```bash
   npm install @popperjs/core
   ```

   Then import it in your JavaScript file (e.g., `resources/js/app.js`):

   ```javascript
   import Popper from '@popperjs/core';
   window.Popper = Popper;
   ```

3. **Register the Tooltip Component**

   Add the Livewire component to your layout or specific view (e.g., `resources/views/layouts/app.blade.php` or `resources/views/livewire/dr/panel/turn/schedule/appointments-list.blade.php`):

   ```blade
   <livewire:miladkermanji-tooltip wire:key="tooltip-component" />
   ```

4. **Clear Cache (Optional)**

   Clear Laravel caches to ensure smooth integration:

   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   composer dump-autoload
   ```

---

## Usage

### Static Tooltips

Add the `tooltip-link` class and `data-tooltip` attribute to any HTML element:

```blade
<button class="tooltip-link" data-tooltip="Cancel appointment" data-placement="top">
    Hover me
</button>
```

### Dynamic Tooltips

Use the `tooltip-method` attribute to call a Livewire method for dynamic content:

```blade
<button class="tooltip-link" tooltip-method="\App\Livewire\MyComponent@getTooltipContent" data-param1="value">
    Dynamic Tooltip
</button>
```

In your Livewire component (e.g., `app/Livewire/MyComponent.php`):

```php
namespace App\Livewire;

use Livewire\Component;

class MyComponent extends Component
{
    public function getTooltipContent($param1)
    {
        return "Dynamic content: $param1";
    }

    public function render()
    {
        return view('livewire.my-component');
    }
}
```

### Example in a Real Project

In `resources/views/livewire/dr/panel/turn/schedule/appointments-list.blade.php`, you can use the tooltip for action buttons:

```blade
<button class="btn btn-light rounded-circle shadow-sm tooltip-link"
        data-tooltip="جابجایی نوبت"
        data-placement="top"
        wire:click="$dispatch('showModal', {data: {'alias': 'reschedule-modal', 'params': {'appointmentId': {{ $appointment->id }}}}})"
        {{ $appointment->status === 'cancelled' || $appointment->status === 'attended' ? 'disabled' : '' }}>
    <img src="{{ asset('dr-assets/icons/rescheule-appointment.svg') }}" alt="جابجایی">
</button>

<button class="btn btn-light rounded-circle shadow-sm tooltip-link"
        data-tooltip="لغو نوبت"
        data-placement="top"
        wire:click="cancelSingleAppointment({{ $appointment->id }})"
        {{ $appointment->status === 'cancelled' || $appointment->status === 'attended' ? 'disabled' : '' }}>
    <img src="{{ asset('dr-assets/icons/cancle-appointment.svg') }}" alt="لغو">
</button>
```

---

## Configuration

You can publish the configuration file to customize default settings:

```bash
php artisan vendor:publish --tag=livewire-tooltip-config
```

This will create a `config/livewire-tooltip.php` file with the following defaults:

```php
<?php

return [
    'default_placement' => 'top', // Options: top, right, bottom, left
    'animation_duration' => 300,  // Duration in milliseconds
];
```

---

## Debugging

If tooltips don't appear or show empty content:

1. **Check Popper.js**:
   Ensure Popper.js is loaded:
   ```javascript
   console.log('Popper:', typeof Popper !== 'undefined');
   ```

2. **Inspect Tooltip Links**:
   Verify that elements have the `tooltip-link` class and valid `data-tooltip` or `tooltip-method` attributes:
   ```javascript
   console.log('Tooltip links:', document.querySelectorAll('.tooltip-link').length);
   ```

3. **Check Console Logs**:
   Look for warnings or errors:
   ```javascript
   console.warn('Invalid tooltip link:', ...);
   console.debug('Tooltip shown:', ...);
   ```

4. **Review Laravel Logs**:
   Check `storage/logs/laravel.log` for any errors related to the tooltip component.

---

## Contributing

Contributions are welcome! Please submit a pull request or open an issue on the [GitHub repository](https://github.com/miladkermaji/miladkermanji-livewire-tooltip).

---

## License

This package is open-sourced under the [MIT License](LICENSE).

---

## About

Created by [Miladkermanji](mailto:miladkermanji8639@gmail.com). Built with ❤️ for the Laravel community.