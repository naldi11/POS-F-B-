<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'cashier';
    public $is_active = true;
    
    public $editingId = null;
    public $isOpen = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;
        $this->isOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'password' => $this->editingId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|in:admin,cashier,kitchen,marketing',
            'is_active' => 'boolean',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            session()->flash('message', 'Data pengguna berhasil diupdate.');
        } else {
            User::create($data);
            session()->flash('message', 'Pengguna baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        if (auth()->id() == $id) {
            session()->flash('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        session()->flash('message', 'Status pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (auth()->id() == $id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);
        $userName = $user->name;
        $user->delete();

        session()->flash('message', 'Pengguna ' . $userName . ' berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->is_active = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $query = User::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->roleFilter)) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users,
        ]);
    }
}
