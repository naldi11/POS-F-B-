<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Table;
use App\Models\Bundle;
use Livewire\Attributes\Layout;

#[Layout('layouts.customer')]
class MenuList extends Component
{
    public $table_number;
    public $categories;
    public $selectedCategory = null;
    
    // Modal state
    public $selectedMenu = null;
    public $selectedBundle = null;
    public $quantity = 1;
    public $notes = '';
    public $showModal = false;
    public $isBundleModal = false;

    public function mount()
    {
        if (request()->has('table')) {
            session(['table_number' => request()->query('table')]);
        }
        $this->table_number = session('table_number');

        $this->categories = Category::where('is_active', true)->get();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function openDetail($menuId)
    {
        $this->selectedMenu = Menu::find($menuId);
        if ($this->selectedMenu && $this->selectedMenu->is_available) {
            $this->quantity = 1;
            $this->notes = '';
            $this->isBundleModal = false;
            $this->showModal = true;
        } else {
            session()->flash('error', 'Menu ini sedang tidak tersedia.');
        }
    }

    public function openBundleDetail($bundleId)
    {
        $this->selectedBundle = Bundle::with('items.menu')->find($bundleId);
        if ($this->selectedBundle && $this->selectedBundle->is_active) {
            $this->quantity = 1;
            $this->notes = '';
            $this->isBundleModal = true;
            $this->showModal = true;
        } else {
            session()->flash('error', 'Paket ini sedang tidak tersedia.');
        }
    }

    public function closeDetail()
    {
        $this->showModal = false;
        $this->selectedMenu = null;
        $this->selectedBundle = null;
        $this->isBundleModal = false;
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        
        if ($this->isBundleModal) {
            if (!$this->selectedBundle || !$this->selectedBundle->is_active) {
                session()->flash('error', 'Paket ini sedang tidak tersedia.');
                return;
            }

            $notesHash = md5(trim($this->notes));
            $cartKey = 'bundle_' . $this->selectedBundle->id . '_' . $notesHash;

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $this->quantity;
            } else {
                $cart[$cartKey] = [
                    'bundle_id' => $this->selectedBundle->id,
                    'name' => $this->selectedBundle->name,
                    'price' => $this->selectedBundle->price,
                    'quantity' => $this->quantity,
                    'image' => $this->selectedBundle->image,
                    'notes' => trim($this->notes),
                    'is_bundle' => true
                ];
            }
            $name = $this->selectedBundle->name;

        } else {
            if (!$this->selectedMenu || !$this->selectedMenu->is_available) {
                session()->flash('error', 'Menu ini sedang tidak tersedia.');
                return;
            }

            $notesHash = md5(trim($this->notes));
            $cartKey = $this->selectedMenu->id . '_' . $notesHash;

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $this->quantity;
            } else {
                $cart[$cartKey] = [
                    'menu_id' => $this->selectedMenu->id,
                    'name' => $this->selectedMenu->name,
                    'price' => $this->selectedMenu->price,
                    'quantity' => $this->quantity,
                    'image' => $this->selectedMenu->image,
                    'notes' => trim($this->notes),
                    'is_bundle' => false
                ];
            }
            $name = $this->selectedMenu->name;
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        session()->flash('message', $name . ' ditambahkan ke keranjang!');
        
        $this->closeDetail();
    }

    public function render()
    {
        $query = Menu::where('is_available', true);
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        $bundles = collect();
        if (!$this->selectedCategory) {
            $bundles = Bundle::where('is_active', true)->with('items.menu')->get();
        }

        return view('livewire.customer.menu-list', [
            'menus' => $query->get(),
            'bundles' => $bundles,
            'cartCount' => collect(session()->get('cart', []))->sum('quantity')
        ]);
    }
}
