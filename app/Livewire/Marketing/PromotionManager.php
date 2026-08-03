<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Promotion;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PromotionManager extends Component
{
    use WithPagination;

    public $code, $type = 'percentage', $value, $min_purchase = 0;
    public $max_uses = null;
    public $valid_from, $valid_until, $is_active = true;
    public $editingId = null;

    protected $rules = [
        'code' => 'required|unique:promotions,code',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'max_uses' => 'nullable|integer|min:1',
        'min_purchase' => 'required|numeric|min:0',
        'valid_from' => 'nullable|date',
        'valid_until' => 'nullable|date|after_or_equal:valid_from',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['code'] = 'required|unique:promotions,code,' . $this->editingId;
        }

        $this->validate($rules);

        Promotion::updateOrCreate(
            ['id' => $this->editingId],
            [
                'code' => strtoupper($this->code),
                'type' => $this->type,
                'value' => $this->value,
                'max_uses' => $this->max_uses ?: null,
                'min_purchase' => $this->min_purchase,
                'valid_from' => $this->valid_from ?: null,
                'valid_until' => $this->valid_until ?: null,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->editingId ? 'Promo berhasil diupdate.' : 'Promo berhasil dibuat.');
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $promo = Promotion::findOrFail($id);
        $this->editingId = $id;
        $this->code = $promo->code;
        $this->type = $promo->type;
        $this->value = (float) $promo->value;
        $this->max_uses = $promo->max_uses;
        $this->min_purchase = (float) $promo->min_purchase;
        $this->valid_from = $promo->valid_from ? \Carbon\Carbon::parse($promo->valid_from)->format('Y-m-d\TH:i') : null;
        $this->valid_until = $promo->valid_until ? \Carbon\Carbon::parse($promo->valid_until)->format('Y-m-d\TH:i') : null;
        $this->is_active = $promo->is_active;
    }

    public function delete($id)
    {
        Promotion::findOrFail($id)->delete();
        session()->flash('message', 'Promo berhasil dihapus.');
    }

    public function resetInputFields()
    {
        $this->code = '';
        $this->type = 'percentage';
        $this->value = '';
        $this->max_uses = null;
        $this->min_purchase = 0;
        $this->valid_from = null;
        $this->valid_until = null;
        $this->is_active = true;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.marketing.promotion-manager', [
            'promotions' => Promotion::latest()->paginate(10)
        ]);
    }
}
