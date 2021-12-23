<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductsController extends Component
{
    use WithPagination;
    use WithFileUploads;
    
    public $name, $barcode, $cost, $price, $stock, $alerts, $image, $categoryid, $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;

    public function paginationView(){
        return 'vendor.livewire.bootstrap';
    }

    public function mount(){
        $this->pageTitle = 'Listado';
        $this->componentName = 'Productos';
        $this->categoryid = 'Elegir';
   }

   public function render()
   {
        if (strlen($this->search) > 0) {
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                        ->select('products.*', 'c.name as category')
                        ->where('products.name', 'like', '%'.$this->search.'%')
                        ->orWhere('products.barcode', 'like', '%'.$this->search.'%')
                        ->orWhere('c.name', 'like', '%'.$this->search.'%')
                        ->orderBy('products.name', 'asc')
                        ->paginate($this->pagination);
        }else{
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                        ->select('products.*', 'c.name as category')
                        ->orderBy('products.name', 'asc')
                        ->paginate($this->pagination);
        }

       return view('livewire.products.component', [
           'data' => $products,
           'categories' => Category::orderBy('name', 'asc')->get()
       ])->extends('layouts.theme.app')->section('content');
   }

   public function Store()
   {
        $rules = [
            'name' => 'required|unique:products|min:3',
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir'
        ];
        $messages = [
            'name.required' => 'Nombre del Producto es requerido',
            'name.unique' => 'Ya existe el nombre del producto',
            'name.min' => 'El nombre del producto debe tener al menos 3 caracteres',
            'cost.required' => 'El costo es requerido',
            'price.required' => 'El precio es requerido',
            'stock.required' => 'El stock es requerido',
            'alerts.required' => 'Ingresa el valor mínima en existencias',
            'categoryid.not_in' => 'Elige un nombre de categoría diferente de Elegir',
        ];
        $this->validate($rules, $messages);

        $product = Product::create([
            'name' => $this->name,
            'cost' => $this->cost,
            'price' => $this->price,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid,
        ]);

        $customFilename;
        if($this->image){
            $customFilename = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFilename);
            $product->image = $customFilename;
            $product->save();
        }

        $this->resetUI(); // Limpiar las cajas de texto del formulario
        $this->emit('product-added','Producto Registrado');
   }

   // Para poder cerrar la ventana modal
   public function resetUI(){
        // $this->name = '';
        // $this->image = null;
        // $this->search = '';
        // $this->selected_id = 0;
    }

}
