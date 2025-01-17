<?php

namespace App\Livewire;

use App\Enums\OrderGender;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use Filament\Forms\Form;
use Livewire\Component;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Cashier extends Component implements HasForms
{
    use InteractsWithForms;

    public $search = '';
    public $customer_name = null;
    public $payment_method_id = null;
    public $cartItems = [];
    public $totalPrice;

    public function mount(): void
    {
        if (Session::has('cartItems')) {
            $this->cartItems = Session::get('cartItems');
        }

        $this->form->fill();

        $this->calculateTotalPriceInCart();
    }

    public function render()
    {
        $products = Product::query()
            ->where('stock', '!=', 0)
            ->where('is_active', true)
            ->search($this->search)
            ->orderBy('name')
            ->paginate(9);

        return view('livewire.cashier', compact('products'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Checkout Form')
                ->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->required()
                        ->maxLength(255)
                        ->default(fn() => $this->customer_name)
                        ->label('Customer Name'),
                    Forms\Components\TextInput::make('totalPrice')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->label('Total Price'),
                    Forms\Components\Select::make('payment_method_id')
                        ->label('Payment Method')
                        ->options(PaymentMethod::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                ])
        ]);
    }

    public function addToCart(int $productId)
    {
        $product = Product::query()->findOrFail($productId);

        if ($product->stock <= 0) {
            Notification::make()
                ->title('Insufficient Product Stock')
                ->body("Product's stock is not enough.")
                ->warning()
                ->send();

            return;
        }

        $existingCartItemKey = null;

        foreach ($this->cartItems as $key => $value) {
            if ($value['product_id'] == $productId) {
                $existingCartItemKey = $key;
                break;
            }
        }

        if ($existingCartItemKey !== null) {
            $this->cartItems[$existingCartItemKey]['product_quantity']++;
        } else {
            $this->cartItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image ? Storage::url($product->image) : 'https://placehold.co/600?text=No+Image',
                'product_quantity' => 1
            ];
        }

        Session::put('cartItems', $this->cartItems);

        $this->calculateTotalPriceInCart();

        Notification::make()
            ->title('Success')
            ->body("Product's added successfully.")
            ->success()
            ->send();
    }

    public function increaseQuantity(int $productId)
    {
        $product = Product::query()->findOrFail($productId);

        foreach ($this->cartItems as $key => $value) {
            if ($value['product_id'] == $productId) {
                if ($value['product_quantity'] + 1 <= $product->stock) {
                    $this->cartItems[$key]['product_quantity']++;
                }
            }
        }

        Session::put('cartItems', $this->cartItems);

        $this->calculateTotalPriceInCart();
    }

    public function decreaseQuantity(int $productId)
    {
        $product = Product::query()->findOrFail($productId);

        foreach ($this->cartItems as $key => $value) {
            if ($value['product_id'] == $productId) {
                if ($this->cartItems[$key]['product_quantity'] > 1) {
                    $this->cartItems[$key]['product_quantity']--;
                } else {
                    unset($this->cartItems[$key]);
                }
            }
        }

        Session::put('cartItems', $this->cartItems);

        $this->calculateTotalPriceInCart();
    }

    public function deleteFromCart($key)
    {
        unset($this->cartItems[$key]);

        Session::forget('cartItems');
        Session::put('cartItems', $this->cartItems);

        $this->calculateTotalPriceInCart();
    }

    public function calculateTotalPriceInCart()
    {
        $initTotal = 0;

        foreach ($this->cartItems as $key => $value) {
            $initTotal += $value['product_quantity'] * $value['product_price'];
        }

        $this->totalPrice = $initTotal;

        return $initTotal;
    }

    public function rules()
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'totalPrice' => ['required', 'integer'],
            'payment_method_id' => ['required', Rule::exists('payment_methods')]
        ];
    }

    public function checkout()
    {
        $this->validate();

        $order = Order::query()->create([
            'name' => $this->customer_name,
            'total_price' => $this->totalPrice,
            'payment_method_id' => $this->payment_method_id,
        ]);

        foreach ($this->cartItems as $key => $value) {
            $order->orderDetails()->create([
                'product_id' => $value['product_id'],
                'quantity' => $value['product_quantity'],
                'price' => $value['product_price']
            ]);
        }

        $this->cartItems = [];

        Session::forget('cartItems');

        return redirect()->to('cashier');
    }
}
