<?php  
  
namespace App\Http\Resources;  
  
use Illuminate\Http\Request;  
use Illuminate\Http\Resources\Json\JsonResource;  
  
class OrderResource extends JsonResource  
{  
    /**  
     * Transform the resource into an array.  
     *  
     * @return array<string, mixed>  
     */  
    public function toArray(Request $request): array  
    {  
        return [  
            'id' => $this->id,  
            'name' => $this->user->name, // Ambil nama dari relasi user  
            'email' => $this->user->email, // Ambil email dari relasi user  
            'phone' => $this->phone,  
            'total_price' => $this->total_price,  
            'note' => $this->note,  
            'paid_amount' => $this->paid_amount,  
            'change_amount' => $this->change_amount,  
            'created_at' => $this->created_at->format('d M Y H:i:s'),  
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails'))  
        ];  
    }  
}  
