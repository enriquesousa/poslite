<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

use Livewire\WithFileUploads; // trait para subir imagenes al backend
use Livewire\WithPagination;

class CategoriesController extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $search, $image, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;

    // Para inicializar propiedades que se van a renderizar en la vista principal del componente
    public function mount(){
         $this->pageTitle = 'Listado';
         $this->componentName = 'Categorías';
    }

    public function render()
    {
        $data = Category::all();

        return view('livewire.category.categories', ['categories' => $data])
            ->extends('layouts.theme.app')
            ->section('content');
    }
}
