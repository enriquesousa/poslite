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
    // es el primer metodo que se ejecuta en los componentes de livewire
    public function mount(){
         $this->pageTitle = 'Listado';
         $this->componentName = 'Categorías';
    }

    public function paginationView(){
        return 'vendor.livewire.bootstrap';
    }

    public function render()
    {
        if(strlen($this->search) > 0){
            $data = Category::where('name', 'like', '%' . $this->search . '%')->paginate($this->pagination);
        }else{
            $data = Category::orderBy('id', 'desc')->paginate($this->pagination);
        }

        return view('livewire.category.categories', ['categories' => $data])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Edit($id){
        $record = Category::find($id, ['id','name','image']);
        $this->name = $record->name;
        $this->selected_id = $record->id;
        $this->image = null;

        $this->emit('show-modal', 'show modal!');
    }

    public function Store(){
        $rules = [
            'name' => 'required|unique:categories|min:3'
        ];
        $messages = [
            'name.required' => 'Nombre de la categoría es requerido',
            'name.unique' => 'Ya existe el nombre de la categoría',
            'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
        ];
        $this->validate($rules, $messages);

        $category = Category::create([
            'name' => $this->name
        ]);

        $customFilename;
        if($this->image){
            $customFilename = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categories', $customFilename);
            $category->image = $customFilename;
            $category->save();
        }

        $this->resetUI(); // Limpiar las cajas de texto del formulario
        $this->emit('category-added','categoría Registrada');
    }

    public function Update(){
        $rules = [
            'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
        ];
        $messages = [
            'name.required' => 'Nombre de Categoría requerido',
            'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
            'name.unique' => 'El Nombre de la Categoría ya existe!',
        ];
        $this->validate($rules, $messages);

        $category = Category::find($this->selected_id);
        $category->update([
            'name' => $this->name
        ]);

        if($this->image){
            $customFilename = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/categories', $customFilename);
            $imageName = $category->image;
            // eliminar la imagen anterior
            $category->image = $customFilename;
            $category->save();
            if ($imageName != null) {
                if (file_exists('storage/categories' . $imageName)) {
                    unlink('storage/categories' . $imageName);
                }
            }
        }

        // Limpiar las cajas de texto
        $this->resetUI();
        $this->emit('category-updated','Categoría Actualizada');
    }

    // Para escuchar los eventos desde el frontend
    protected $listeners = [
        'deleteRow' => 'Destroy'
    ];

    public function Destroy(Category $category){
        // $category = Category::find($id);
        // dd($category);
        $imageName = $category->image; // imagen temporal
        $category->delete();
        if ($imageName != null) {
            unlink('storage/categories/' . $imageName);
        }
        $this->resetUI();
        $this->emit('category-deleted','Categoría Eliminada');
    }


    // Para poder cerrar la ventana modal
    public function resetUI(){
        $this->name = '';
        $this->image = null;
        $this->search = '';
        $this->selected_id = 0;
    }

}
