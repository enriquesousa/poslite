<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Denomination;

class CoinsController extends Component
{
  public $componentName, $pageTitle, $selected_id, $image, $search;

  public function mount(){
    $this->componentName = 'Denominaciones';
    $this->pageTitle = 'Listado';
    $this->selected_id = 0;
  }

  public function render()
  {
      return view('livewire.denominations.component', [
        'data' => Denomination::paginate(5)
      ])->extends('layouts.theme.app')->section('content');
  }
}
