<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.customer')]
class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function increase($menuId)
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
            session()->put('cart', $this->cart);
        }
    }

    public function decrease($menuId)
    {
        if (isset($this->cart[$menuId])) {
            if ($this->cart[$menuId]['quantity'] > 1) {
                $this->cart[$menuId]['quantity']--;
            } else {
                unset($this->cart[$menuId]);
            }
            session()->put('cart', $this->cart);
        }
    }

    public function remove($menuId)
    {
        if (isset($this->cart[$menuId])) {
            unset($this->cart[$menuId]);
            session()->put('cart', $this->cart);
        }
    }

    public function render()
    {
        $total = collect($this->cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('livewire.customer.cart', [
            'total' => $total
        ]);
    }
}
