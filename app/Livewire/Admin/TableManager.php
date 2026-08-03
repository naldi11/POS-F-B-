<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class TableManager extends Component
{
    public $newTableNumber = '';

    public function addTable()
    {
        $this->validate([
            'newTableNumber' => 'required|string|max:50|unique:tables,table_number',
        ], [
            'newTableNumber.unique' => 'Nomor meja ini sudah ada.',
            'newTableNumber.required' => 'Nomor meja wajib diisi.'
        ]);

        \App\Models\Table::create([
            'table_number' => trim($this->newTableNumber),
            'status' => 'available'
        ]);

        $this->newTableNumber = '';
        session()->flash('message', 'Meja berhasil ditambahkan.');
    }

    public function deleteTable($id)
    {
        $table = \App\Models\Table::findOrFail($id);
        
        // Prevent deletion if occupied or has active orders
        if ($table->status === 'occupied' || $table->orders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            session()->flash('error', 'Meja tidak bisa dihapus karena sedang digunakan atau ada pesanan aktif.');
            return;
        }

        $table->delete();
        session()->flash('message', 'Meja berhasil dihapus.');
    }

    public function render()
    {
        $tables = \App\Models\Table::orderByRaw('CAST(table_number AS UNSIGNED), table_number')->get();
        return view('livewire.admin.table-manager', [
            'tables' => $tables
        ]);
    }
}
