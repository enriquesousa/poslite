<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Denomination;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class PosController extends Component
{
    public $total, $itemsQuantity, $efectivo, $change;
    
    public function mount()
    {
        $this->efectivo;
        $this->change;
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
    }

    public function render()
    {
        $this->denominations = Denomination::all();
        return view('livewire.pos.component', [
            'denominations' => Denomination::orderBy('value', 'desc')->get(),
            'cart' => Cart::getContent()->sortBy('name')
        ])
        ->extends('layouts.theme.app')->section('content');
    }

    public function ACash($value)
    {
        $this->efectivo += ($value == 0 ? $this->total : $value);
        $this->change = ($this->efectivo - $this->total);
    }

    protected $listeners = [
        'scan-code' => 'ScanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'saveSale' => 'saveSale'
    ];

    public function ScanCode($barcode, $cant = 1)
    {
        $product = Product::where('barcode', $barcode)->first();
        
        if ($product == null || empty($empty)) {
            $this->emit('scan-notfound', 'El producto no está registrado');
        }else{
            if ($this->InCart($product->id)) {
                $this->increaseQty($product->id);
                return;
            }
            if ($product->stock < 1) {
                $this->emit('no-stock', 'Stock insuficiente :/');
                return;
            }

            //Agregamos producto al carrito
            Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
            $this->total = Cart::getTotal();
            $this->emit('scan-ok', 'Producto agregado');
        }
    }

    // Validar si el ID del producto ya existe en el Carrito
    public function InCart($productId)
    {
        $exist = Cart::get($productId);
        if ($exist) {
            return true;
        }else{
            return false;
        }
    }

    // Actualizar la cantidad de productos en la existencia del carrito
    public function increaseQty($productId, $cant = 1)
    {
        $title = '';
        $product = Product::find($productId);
        
        $exist = Cart::get($productId);
        if ($exist) {
            $title = 'Cantidad actualizada';
        }else{
            $title = 'Producto agregado';
        }

        if ($exist) {
            if ($product->stock < ($cant + $exist->quantity)) {
                $this->emit('no-stock', 'Stock insuficiente :/');
                return;
            }
        }

        // Nota: si el producto ya existe, simplemente el metodo incrementa la cant
        Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
        $this->total = Cart::getTotal();
        $this->itemsQuantity = Cart::getTotalQuantity();
        $this->emit('scan-ok', $title);
    }

    public function updateQty($product, $cant = 1)
    {
        $title = '';
        $product = Product::find($productId);
        $exist = Cart::get($productId);
        if ($exist) {
            $title = 'Cantidad actualizada';
        }else{
            $title = 'Producto agregado';
        }
        if ($exist) {
            if ($product->stock < $cant) {
                $this->emit('no-stock', 'Stock insuficiente :/');
                return;
            }
        }
        // Primero eliminar el producto del carrito
        $this->removeItem($productId);
        // Si cant > 0 Insertar producto en el carrito
        if ($cant > 0) {
            Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
            $this->total = Cart::getTotal();
            $this->itemsQuantity = Cart::getTotalQuantity();
            $this->emit('scan-ok', $title);
        }else{
            $this->emit('scan-ok', 'La Cantidad debe de ser mayor a cero');
        }
    }

}
