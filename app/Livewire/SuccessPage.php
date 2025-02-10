<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Success')]
class SuccessPage extends Component
{
    public function render()
    {

        $latest_order = Order::with('orderDetails')->where('user_id', auth()->user()->id)->latest()->first();
        return view('livewire.success-page',[
            'order' => $latest_order,
        ]);
    }
}
