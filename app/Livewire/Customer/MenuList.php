<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Table;
use Livewire\Attributes\Layout;

#[Layout('layouts.customer')]
class MenuList extends Component
{
    public $table_number;
    public $categories;
    public $selectedCategory = null;
    
    // Modal state
    public $selectedMenu = null;
    public $quantity = 1;
    public $notes = '';
    public $showModal = false;

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
            $this->showModal = true;
        } else {
            session()->flash('error', 'Menu ini sedang tidak tersedia.');
        }
    }

    public function closeDetail()
    {
        $this->showModal = false;
        $this->selectedMenu = null;
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
        if (!$this->selectedMenu || !$this->selectedMenu->is_available) {
            session()->flash('error', 'Menu ini sedang tidak tersedia.');
            return;
        }

        $cart = session()->get('cart', []);
        
        // Generate a unique key based on menu ID and notes (so different notes = different items)
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
                'notes' => trim($this->notes)
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');
        session()->flash('message', $this->selectedMenu->name . ' ditambahkan ke keranjang!');
        
        $this->closeDetail();
    }

    public function render()
    {
        $query = Menu::where('is_available', true);
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return view('livewire.customer.menu-list', [
            'menus' => $query->get(),
            'cartCount' => collect(session()->get('cart', []))->sum('quantity')
        ]);
    }
}
