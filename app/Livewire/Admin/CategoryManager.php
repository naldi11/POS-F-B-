<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class CategoryManager extends Component
{
    use WithPagination;

    public $name;
    public $is_active = true;
    public $editingId = null;
    
    public $isOpen = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'is_active' => 'boolean',
    ];

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        Category::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'is_active' => $this->is_active
            ]
        );

        session()->flash('message', $this->editingId ? 'Kategori berhasil diupdate.' : 'Kategori berhasil ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->is_active = true;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::orderBy('id', 'desc')->paginate(10)
        ]);
    }
}
