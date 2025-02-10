<?php  
namespace App\Livewire;    
    
use App\Models\Order;    
use Livewire\Component;    
use App\Models\OrderDetail;    
use App\Helpers\CartManagement;    
use Illuminate\Support\Facades\Auth;    
    
class CheckoutPage extends Component        
{         
    public $notes;        
    public $phone;        
    public $nomeja; // Properti untuk nama meja    
    public $isSubmitting = false; // Untuk mencegah pengiriman ganda    
    
    public function mount()
    {
        $cartItems = CartManagement::getCartItemsFromCookie();
        if (count($cartItems) == 0) {
            return redirect()->route('products');
        }
    }
  
    public function placeOrder()          
    {          
        // Validasi input          
        $this->validate([          
            'nomeja' => 'required|string|max:2', // Validasi untuk nomeja    
            'phone' => 'required|string|max:20',          
            'notes' => 'nullable|string|max:255',    
        ]);          
            
        // Ambil item keranjang          
        $cartItems = CartManagement::getCartItemsFromCookie();          
        $grandTotal = CartManagement::calculateGrandTotal($cartItems);          
            
        // Cek apakah sudah ada pesanan dengan detail yang sama          
        $existingOrder = Order::where('user_id', Auth::id())          
            ->where('total_price', $grandTotal)          
            ->orderBy('created_at', 'desc') // Ambil pesanan terakhir      
            ->first();          
            
        // Cek apakah pesanan terakhir dilakukan kurang dari 1 menit yang lalu      
        if ($existingOrder && $existingOrder->created_at > now()->subMinute()) {          
            session()->flash('error', 'Anda sudah memesan dengan detail yang sama. Silakan coba lagi setelah 1 menit.');          
            return;          
        }          
            
        // Mencegah pengiriman ganda          
        if ($this->isSubmitting) {          
            return;          
        }          
            
        $this->isSubmitting = true;          
            
        try {
            // Buat pesanan baru          
            $order = new Order();          
            $order->user_id = Auth::id(); // Menyimpan user_id    
            $order->nomeja = $this->nomeja; // Menyimpan nama meja    
            $order->phone = $this->phone; // Menyimpan nomor telepon    
            $order->total_price = $grandTotal;          
            $order->note = $this->notes;         
            $order->payment_status = 0; // Status pembayaran default    
            $order->status = 'new'; // Status pesanan default    
            $order->save();          
                
            // Simpan detail pesanan        
            foreach ($cartItems as $item) {          
                $orderDetail = new OrderDetail();          
                $orderDetail->order_id = $order->id;          
                $orderDetail->product_id = $item['product_id'];          
                $orderDetail->quantity = $item['quantity'];          
                $orderDetail->price = $item['unit_amount'];          
                $orderDetail->save();          
            }          
                
            CartManagement::clearCartItems();          
            $this->isSubmitting = false;          
                
            session()->flash('success', 'Pesanan berhasil dibuat!');
            return redirect()->route('success', $order->id);          
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            session()->flash('error', 'Terjadi kesalahan saat menyimpan pesanan. Silakan coba lagi.');
            return;
        }
    }  

    public function render()        
    {        
        // Get cart items and total price        
        $cartItems = CartManagement::getCartItemsFromCookie();        
        $grandTotal = !empty($cartItems) ? CartManagement::calculateGrandTotal($cartItems) : 0;      
    
        return view('livewire.checkout-page', [        
            'cart_items' => $cartItems,        
            'grand_total' => $grandTotal,        
        ]);        
    }        
}