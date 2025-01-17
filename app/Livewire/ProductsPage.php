<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Produk Mie Kocok H Amsar')]
class ProductsPage extends Component
{

    use LivewireAlert;
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $price_range = 35000;

    #[Url]
    public $sort = 'latest';

    public function addToCart($product_id){
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count:$total_count)->to(Navbar::class);

        $this->alert('success', 'Produk berhasil ditambahkan!', [  
            'position' => 'bottom-end',  
            'timer' => 3000,  
            'toast' => true,  
        ]);  
    }

    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        if(!empty($this->selected_categories)){
            $productQuery->whereIn('category_id', $this->selected_categories);
        }

        if ($this->price_range){
            $productQuery->whereBetween('price',[0, $this->price_range]);
        }

        if ($this->sort == 'latest'){
            $productQuery->latest();
        }

        if ($this->sort == 'price'){
            $productQuery->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $productQuery->paginate(6),
            'categories' =>Category::where('is_active', 1)->get(['id','name','slug']),
        ]);
    }
}
