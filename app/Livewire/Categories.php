<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    #[Validate('required|string|max:255')]
    public $categoryName;
    #[Validate('required|string|max:255')]
    public $status;
    #[Validate('required|integer')]
    public $parentCategory;
    public $editCategory;
    public $editCategoryName;
    public $editParentCategory;
    public $editStatus;

    public $slug = '';

    public $query;
    public $parents;

    public function mount()
    {
        $this->parents = Category::with('children')->whereNull('parent_id')->get();
    }


    protected $rules = [
        'categoryName' => 'required|unique:categories,name',
        'status' => 'required',
    ];

    public function saveCategory()
    {
        $this->validate();

        Category::query()->create([
            'slug' => uniqid(),
            'name' => $this->categoryName,
            'status' => $this->status,
            'parent_id' => is_numeric($this->parentCategory) ? $this->parentCategory : null,
            'created_by' => Auth::id(),
        ]);

        $this->resetInputFields();
        $this->dispatch('categoryUpdated'); // Emit an event to refresh the table
    }

    public function edit(Category $category)
    {
        $this->editCategory = $category;
        $this->editCategoryName = $category->name;
        $this->editParentCategory = $category->parent_id;
        $this->editStatus = $category->status;
        $this->slug = $category->slug;

        $this->dispatch('show-edit-modal', category: $category);
    }

    public function update()
    {
        $this->validate([
            'editCategoryName' => 'required',
            'editParentCategory' => 'required',
            'editStatus' => 'required',
        ]);
        //    dd($this->editCategoryName);
        $category = Category::query()->where('slug', $this->slug)->first();

        $category->update([
            'name' => $this->editCategoryName,
            'parent_id' => $this->editParentCategory,
            'status' => $this->editStatus,
        ]);

        $this->resetInputFields();
        $this->dispatch('modal-hide'); // Emit an event to refresh the table
    }

    private function resetInputFields()
    {
        $this->categoryName = '';
        $this->status = '';
        $this->editCategory = null;
        $this->editCategoryName = '';
        $this->editStatus = '';
        $this->parentCategory = '';
        $this->editParentCategory = '';
    }


    public function search()
    {
        return Category::query()->with('parent')->when($this->query, function ($query){
            $query->whereLike(['name'], $this->query);
        })->latest()->paginate(9);
    }

    public function clear()
    {
        $this->query = '';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.categories', [
            'categories' => $this->search(),
            'parents' => Category::query()->latest()->get(),
        ]);
    }
}
