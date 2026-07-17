<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/');
    }
}; ?>

<header class="sticky top-0 z-40 flex w-full bg-white drop-shadow-1 dark:bg-boxdark border-b border-gray-200 shadow-sm">
    <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
        
        <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
            <!-- Hamburger Toggle BTN -->
            <button @click.stop="sidebarOpen = !sidebarOpen" class="z-50 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm lg:hidden">
                <span class="relative block h-5 w-5 cursor-pointer">
                    <span class="block absolute right-0 h-full w-full">
                        <span class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out" :class="{ '!w-full delay-300': !sidebarOpen }"></span>
                        <span class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out" :class="{ '!w-full delay-400': !sidebarOpen }"></span>
                        <span class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out" :class="{ '!w-full delay-500': !sidebarOpen }"></span>
                    </span>
                    <span class="block absolute right-0 h-full w-full rotate-45">
                        <span class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out" :class="{ '!h-0 delay-[0]': !sidebarOpen }"></span>
                        <span class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out" :class="{ '!h-0 delay-200': !sidebarOpen }"></span>
                    </span>
                </span>
            </button>
            <!-- Hamburger Toggle BTN -->
            
            <a class="block flex-shrink-0 lg:hidden" href="{{ route('dashboard') }}">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </a>
        </div>

        <div class="hidden sm:block">
            <!-- Breadcrumbs or Search could go here -->
        </div>

        <div class="flex items-center gap-3 2xsm:gap-7">
            <!-- User Area -->
            <div class="relative">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-4 focus:outline-none">
                            <span class="hidden text-right lg:block">
                                <span class="block text-sm font-medium text-black" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                                <span class="block text-xs font-medium">{{ auth()->user()->role }}</span>
                            </span>
                            
                            <span class="h-11 w-11 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>

                            <svg class="hidden fill-current sm:block w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
            <!-- User Area -->
        </div>
    </div>
</header>
