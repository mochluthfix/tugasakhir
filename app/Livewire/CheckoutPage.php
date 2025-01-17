<?php   
  
namespace App\Livewire;  
  
use Midtrans\Snap;  
use Midtrans\Config;  
use App\Models\Order;  
use Livewire\Component;  
use App\Models\PaymentMethod;  
use Livewire\Attributes\Title;  
use App\Helpers\CartManagement;  
  
#[Title('Checkout')]  
class CheckoutPage extends Component    
{    
    public $nama;    
    public $notes;    
    public $phone;    
    public $payment_method;    
    public $snapToken;  
  
    public function placeOrder()        
{        
    // Validate input        
    $this->validate([        
        'nama' => 'required|string|max:255',        
        'phone' => 'nullable|string|max:15',        
        'notes' => 'nullable|string|max:255',        
        'payment_method' => 'required|string'        
    ]);        
  
    // Get cart items from cookie        
    $cart_items = CartManagement::getCartItemsFromCookie();        
    $line_items = [];        
    foreach ($cart_items as $item) {        
        $line_items[] = [        
            'price' => $item['unit_amount'],        
            'quantity' => $item['quantity'],        
            'name' => $item['name']        
        ];        
    }      
          
    $order = new Order();  
    $order->name = $this->nama;
    $order->total_price = CartManagement::calculateGrandTotal($cart_items);        
    $order->note = $this->notes;  
  
    // Set Midtrans configuration      
    Config::$serverKey = config('midtrans.serverKey');  
    Config::$clientKey = config('midtrans.clientKey');      
    Config::$isProduction = config('services.midtrans.isProduction');      
    Config::$isSanitized = config('services.midtrans.isSanitized');      
    Config::$is3ds = config('services.midtrans.is3ds');      
  
    if ($this->payment_method === 'qris') {  
        // Create transaction for QRIS  
        $transaction_details = [        
            'order_id' => uniqid(), // Generate unique order ID        
            'gross_amount' => $order->total_price, // Total amount        
        ];        
  
        $item_details = [];        
        foreach ($line_items as $item) {        
            $item_details[] = [        
                'id' => $item['name'],        
                'price' => $item['price'],        
                'quantity' => $item['quantity'],        
                'name' => $item['name'],        
            ];        
        }        
  
        $transaction_data = [        
            'transaction_details' => $transaction_details,        
            'item_details' => $item_details,        
            'customer_details' => [        
                'first_name' => $this->nama,        
                'phone' => $this->phone,        
                'notes' => $this->notes,        
            ],        
        ];             
  
        $snapToken = \MidTrans\Snap::getSnapToken($transaction_data);      
          
        $order->snap_token = $snapToken;  
        $this->dispatch('openMidtrans', ['snapToken' => $this->snapToken]);  
        $order->save();  

    } else {  
        // Save order directly for "Bayar di Kasir"  
        $order->save();  
        return redirect()->route('success', $order->id); // Redirect to success page  
        CartManagement::clearCartItems();
    }  
}  
   
  
    public function render()    
    {    
        // Get cart items and total price    
        $cart_items = CartManagement::getCartItemsFromCookie();    
        $grand_total = !empty($cart_items) ? CartManagement::calculateGrandTotal($cart_items) : 0;  
          
  
        return view('livewire.checkout-page', [    
            'cart_items' => $cart_items,    
            'grand_total' => $grand_total, 
            'snapToken' => $this->snapToken,   
        ]);    
    }    
}  
