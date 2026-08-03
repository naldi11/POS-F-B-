<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Bundle;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class BundleManager extends Component
{
    use WithFileUploads, WithPagination;

    public $name, $description, $price, $image, $is_active = true;
    public $bundleId;
    public $isModalOpen = false;
    public $remove_existing_image = false;
    public $remoteImageUrl = null;
    public $bundleItems = [];
    public $availableMenus = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
        'bundleItems' => 'required|array|min:1',
        'bundleItems.*.menu_id' => 'required|exists:menus,id',
        'bundleItems.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->availableMenus = Menu::where('is_available', true)->get();
        $this->addBundleItem();
    }

    public function render()
    {
        return view('livewire.marketing.bundle-manager', [
            'bundles' => Bundle::with('items.menu')->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->image = null;
        $this->is_active = true;
        $this->bundleId = null;
        $this->remove_existing_image = false;
        $this->remoteImageUrl = null;
        $this->bundleItems = [];
        $this->addBundleItem();
    }

    public function handleRemoteImage($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) || str_starts_with($url, 'data:image')) {
            $this->remoteImageUrl = $url;
            $this->image = null;
            $this->remove_existing_image = true;
        }
    }

    public function removeImage()
    {
        $this->image = null;
        $this->remoteImageUrl = null;
        $this->remove_existing_image = true;
    }

    public function addBundleItem()
    {
        $this->bundleItems[] = ['menu_id' => '', 'quantity' => 1];
    }

    public function removeBundleItem($index)
    {
        unset($this->bundleItems[$index]);
        $this->bundleItems = array_values($this->bundleItems);
    }

    public function store()
    {
        $this->validate();

        $imagePath = $this->bundleId ? Bundle::find($this->bundleId)->image : null;

        if ($this->remove_existing_image && !$this->image && !$this->remoteImageUrl) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if ($this->image) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image->store('bundles', 'public');
        } elseif ($this->remoteImageUrl) {
            try {
                $contents = null;
                if (str_starts_with($this->remoteImageUrl, 'data:image')) {
                    $parts = explode(',', $this->remoteImageUrl);
                    if (count($parts) === 2) {
                        $contents = base64_decode($parts[1]);
                    }
                } else {
                    $context = stream_context_create(['http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"]]);
                    $contents = @file_get_contents($this->remoteImageUrl, false, $context);
                }

                if ($contents) {
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $filename = 'bundles/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($filename, $contents);
                    $imagePath = $filename;
                }
            } catch (\Exception $e) {}
        }

        $bundle = Bundle::updateOrCreate(
            ['id' => $this->bundleId],
            [
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'image' => $imagePath,
                'is_active' => $this->is_active,
            ]
        );

        $bundle->items()->delete();
        foreach ($this->bundleItems as $item) {
            $bundle->items()->create([
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        session()->flash('message', $this->bundleId ? 'Paket berhasil diperbarui.' : 'Paket berhasil ditambahkan.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $bundle = Bundle::with('items')->findOrFail($id);
        $this->bundleId = $id;
        $this->name = $bundle->name;
        $this->description = $bundle->description;
        $this->price = $bundle->price;
        $this->is_active = $bundle->is_active;
        $this->image = null;
        $this->remoteImageUrl = null;
        $this->remove_existing_image = false;
        
        $this->bundleItems = $bundle->items->map(function ($item) {
            return [
                'menu_id' => $item->menu_id,
                'quantity' => $item->quantity
            ];
        })->toArray();

        $this->openModal();
    }

    public function delete($id)
    {
        $bundle = Bundle::findOrFail($id);
        if ($bundle->image && Storage::disk('public')->exists($bundle->image)) {
            Storage::disk('public')->delete($bundle->image);
        }
        $bundle->delete();
        session()->flash('message', 'Paket berhasil dihapus.');
    }
}
