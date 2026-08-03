<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Menu;
use App\Models\Category;

class MenuManager extends Component
{
    use WithPagination, WithFileUploads;

    public $category_id, $name, $description, $price, $image, $is_available = true;
    public $best_seller_status = 'auto';
    public $editingId = null;
    public $isOpen = false;
    public $existingImage = null;
    public $downloadedImage = null;

    protected function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => $this->editingId ? 'nullable|image|max:51200' : 'nullable|image|max:51200',
            'is_available' => 'boolean',
            'best_seller_status' => 'required|in:auto,yes,no',
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $this->editingId = $id;
        $this->category_id = $menu->category_id;
        $this->name = $menu->name;
        $this->description = $menu->description;
        $this->price = $menu->price;
        $this->is_available = $menu->is_available;
        $this->best_seller_status = $menu->best_seller_status;
        $this->existingImage = $menu->image;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'is_available' => $this->is_available,
            'best_seller_status' => $this->best_seller_status
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('menus', 'public');
        } elseif ($this->downloadedImage) {
            $data['image'] = $this->downloadedImage;
        }

        Menu::updateOrCreate(['id' => $this->editingId], $data);

        session()->flash('message', $this->editingId ? 'Menu berhasil diupdate.' : 'Menu berhasil ditambahkan.');
        $this->closeModal();
    }

    public function uploadFromUrl($url)
    {
        // Handle base64 data URI
        if (str_starts_with($url, 'data:image')) {
            try {
                preg_match('/data:image\/(?<mime>.*?)\;/', $url, $groups);
                $ext = $groups['mime'] ?? 'jpg';
                if ($ext === 'jpeg') $ext = 'jpg';
                
                $data = explode(',', $url);
                $content = base64_decode($data[1]);
                
                $filename = 'menus/' . \Illuminate\Support\Str::random(40) . '.' . $ext;
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $content);
                $this->downloadedImage = $filename;
                $this->image = null;
                return;
            } catch (\Exception $e) {
                $this->addError('image', 'Data gambar tidak valid.');
                return;
            }
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get($url);
            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                if (str_starts_with($contentType, 'image/')) {
                    $ext = explode('/', $contentType)[1];
                    if ($ext === 'jpeg') $ext = 'jpg';
                    if (str_contains($ext, ';')) $ext = explode(';', $ext)[0];
                    
                    $filename = 'menus/' . \Illuminate\Support\Str::random(40) . '.' . $ext;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());
                    $this->downloadedImage = $filename;
                    $this->image = null; // Reset local upload
                } else {
                    $this->addError('image', 'URL bukan file gambar (Tipe: ' . $contentType . '). Coba copy image address.');
                }
            } else {
                $this->addError('image', 'Gagal mengunduh gambar (Status ' . $response->status() . '). Mungkin diblokir oleh server.');
            }
        } catch (\Exception $e) {
            $this->addError('image', 'URL tidak dapat diakses.');
        }
    }

    public function removeImage()
    {
        $this->image = null;
        $this->downloadedImage = null;
    }

    public function delete($id)
    {
        Menu::find($id)->delete();
        session()->flash('message', 'Menu berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->category_id = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->image = null;
        $this->existingImage = null;
        $this->downloadedImage = null;
        $this->is_available = true;
        $this->best_seller_status = 'auto';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.admin.menu-manager', [
            'menus' => Menu::with('category')->orderBy('id', 'desc')->paginate(10),
            'categories' => Category::where('is_active', true)->get()
        ]);
    }
}
