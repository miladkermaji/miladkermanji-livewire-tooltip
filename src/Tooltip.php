<?php

namespace Miladkermanji\LivewireTooltip;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

/**
 * A Livewire component for displaying tooltips using Popper.js.
 * Supports static tooltips via data-tooltip and dynamic content via tooltip-method.
 *
 * Usage:
 * <livewire:tooltip wire:key="tooltip-component" />
 * <button class="tooltip-link" data-tooltip="Tooltip text" data-placement="top">Hover me</button>
 * <button class="tooltip-link" tooltip-method="\App\Livewire\Class@method" data-param1="value">Dynamic</button>
 */
class Tooltip extends Component
{
    public $toolTipHtml = '';

    public function render()
    {
        return <<<'HTML'
        <div>
            <style>
                #tooltip[data-show] {
                    display: block;
                    z-index: 9999;
                    opacity: 1;
                    transition: opacity 0.3s ease-in-out;
                }

                #tooltip {
                    display: none;
                    background-color: #333;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-family: inherit;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                }

                .loading-spinner {
                    width: 1rem;
                    height: 1rem;
                    border: 2px solid white;
                    border-top: 2px solid transparent;
                    border-radius: 50%;
                    display: inline-block;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    to {
                        transform: rotate(360deg);
                    }
                }
            </style>
            <div wire:ignore.self id="tooltip">
                <div class="flex items-center gap-2">
                    <div>{!! $this->toolTipHtml !!}</div>
                    <div wire:loading>
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>
            @script
            <script>
                (function () {
                    const container = document.querySelector('body');
                    let tooltip = document.getElementById('tooltip');
                    let popperInstance = null;
                    let timeout = null;

                    function initializeTooltip() {
                        // Remove previous listeners
                        container.removeEventListener('mouseover', handleMouseOver);
                        container.removeEventListener('mouseout', handleMouseOut);

                        const tooltipLinks = document.querySelectorAll('.tooltip-link');

                        function handleMouseOver(event) {
                            if (!event.target.classList.contains('tooltip-link')) return;

                            const placement = event.target.getAttribute('data-placement') || 'top';
                            const tooltipText = event.target.getAttribute('data-tooltip');
                            const classAndMethod = event.target.getAttribute('tooltip-method');

                            // Skip invalid tooltips
                            if (!tooltipText && !classAndMethod) {
                                console.warn('Invalid tooltip link:', event.target.outerHTML);
                                return;
                            }

                            // Destroy previous Popper instance
                            if (popperInstance) {
                                popperInstance.destroy();
                                popperInstance = null;
                            }

                            // Create new Popper instance
                            popperInstance = Popper.createPopper(event.target, tooltip, {
                                placement: placement,
                                modifiers: [
                                    {
                                        name: 'offset',
                                        options: {
                                            offset: [0, 8],
                                        },
                                    },
                                    {
                                        name: 'preventOverflow',
                                        options: {
                                            padding: 8,
                                        },
                                    },
                                ],
                            });

                            function showTooltip() {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {
                                    tooltip.setAttribute('data-show', '');
                                    tooltip.classList.remove('hidden');
                                    popperInstance.update();
                                }, 100); // Delay for smoother animation
                            }

                            function hideTooltip() {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => {
                                    tooltip.removeAttribute('data-show');
                                    tooltip.classList.add('hidden');
                                    if (popperInstance) {
                                        popperInstance.destroy();
                                        popperInstance = null;
                                    }
                                }, 100); // Delay for smoother animation
                            }

                            // Set content
                            if (tooltipText) {
                                tooltip.innerHTML = `<div class="flex items-center gap-2"><div>${tooltipText}</div></div>`;
                                showTooltip();
                            } else if (classAndMethod) {
                                const params = {};
                                Object.entries(event.target.dataset).forEach(([key, value]) => {
                                    if (key.startsWith('param')) {
                                        params[key.replace(/^param/, '').toLowerCase()] = value;
                                    }
                                });
                                tooltip.innerHTML = `<div class="flex items-center gap-2"><div wire:loading><div class="loading-spinner"></div></div></div>`;
                                showTooltip();
                                Livewire.dispatchTo('miladkermanji-tooltip', 'tooltip-mouseover', {
                                    classAndMethod: classAndMethod,
                                    parameters: params
                                });
                            }

                            // Debug log
                            console.debug('Tooltip shown:', {
                                element: event.target.outerHTML,
                                tooltipText: tooltipText,
                                classAndMethod: classAndMethod
                            });
                        }

                        function handleMouseOut(event) {
                            if (!event.target.classList.contains('tooltip-link')) return;
                            clearTimeout(timeout);
                            timeout = setTimeout(() => {
                                tooltip.removeAttribute('data-show');
                                tooltip.classList.add('hidden');
                                if (popperInstance) {
                                    popperInstance.destroy();
                                    popperInstance = null;
                                }
                                Livewire.dispatchTo('miladkermanji-tooltip', 'tooltip-mouseout');
                            }, 100);
                        }

                        container.addEventListener('mouseover', handleMouseOver);
                        container.addEventListener('mouseout', handleMouseOut);
                    }

                    // Initialize on load
                    initializeTooltip();

                    // Re-initialize after Livewire updates
                    document.addEventListener('livewire:updated', initializeTooltip);
                })();
            </script>
            @endscript
        </div>
        HTML;
    }

    #[On('tooltip-mouseover')]
    public function fetchContent($classAndMethod, $parameters = [])
    {
        Log::debug('fetchContent called', ['classAndMethod' => $classAndMethod, 'parameters' => $parameters]);

        if (empty($classAndMethod)) {
            Log::warning('fetchContent: classAndMethod is empty');
            return '';
        }

        $parsed = $this->parseClassAndMethodName($classAndMethod);

        if (is_null($parsed['class']) || is_null($parsed['method'])) {
            Log::warning('fetchContent: Invalid class or method', ['parsed' => $parsed]);
            return '';
        }

        [$class, $method] = [$parsed['class'], $parsed['method']];

        try {
            $result = call_user_func_array([app($class), $method], array_values($parameters));
            $this->toolTipHtml = is_string($result) ? $result : '';
        } catch (\Exception $e) {
            Log::error('fetchContent failed', ['error' => $e->getMessage(), 'classAndMethod' => $classAndMethod]);
            $this->toolTipHtml = '';
        }

        return $this->toolTipHtml;
    }

    #[On('tooltip-mouseout')]
    public function clearContent()
    {
        $this->toolTipHtml = '';
    }

    protected function parseClassAndMethodName($classAndMethod)
    {
        Log::debug('Parsing class and method', ['classAndMethod' => $classAndMethod]);

        if (empty($classAndMethod)) {
            Log::warning('parseClassAndMethodName: classAndMethod is empty');
            return ['class' => null, 'method' => null];
        }

        $classAndMethod = str_replace('\\\\', '\\', $classAndMethod);

        if (!str_contains($classAndMethod, '@')) {
            return [
                'class' => \App\Livewire\Dr\Panel\Turn\Schedule\AppointmentsList::class,
                'method' => $classAndMethod
            ];
        }

        [$class, $method] = explode('@', $classAndMethod, 2);

        if (empty($class) || empty($method)) {
            Log::warning('parseClassAndMethodName: Invalid format', ['classAndMethod' => $classAndMethod]);
            return ['class' => null, 'method' => null];
        }

        return compact('class', 'method');
    }
}