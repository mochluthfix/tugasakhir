<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('HomePage - Mie Kocok H Amsar')]
class HomePage extends Component
{
    public function render()
    {
        $products = Product::where('is_active', 1)->get();
        return view('livewire.home-page',[
            'products' => $products
        ]);
    }
}
