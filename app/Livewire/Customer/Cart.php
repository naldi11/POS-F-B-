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

    public function increase($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
            session()->put('cart', $this->cart);
        }
    }

    public function decrease($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] > 1) {
                $this->cart[$cartKey]['quantity']--;
            } else {
                unset($this->cart[$cartKey]);
            }
            session()->put('cart', $this->cart);
        }
    }

    public function remove($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            unset($this->cart[$cartKey]);
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
