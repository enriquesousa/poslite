<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Denomination;

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


}
