<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
	<h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
		Checkout
	</h1>
	<form wire:submit.prevent="placeOrder">
		<div class="grid grid-cols-12 gap-4">
			<div class="md:col-span-12 lg:col-span-8 col-span-12">
				<!-- Card -->
				<div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
					<!-- Shipping Address -->
					<div class="mb-6">
						<h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
							Pelanggan
						</h2>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label class="block text-gray-700 dark:text-white mb-1" for="first_name">
									Nama
								</label>
								<input wire:model='nama' class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('nama')border-red-400 @enderror " id="first_name" type="text">
								</input>
								@error('nama')
									<div class="text-red-400 text-sm">{{$message}}</div>
								@enderror
							</div>
						</div>
						<div class="mt-4">  
							<label class="block text-gray-700 dark:text-white mb-1" for="phone" id="phoneLabel">  
								No HP <span class="text-gray-500">(Optional)</span>  
							</label>  
							<input wire:model='phone' class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="phone" type="text" oninput="toggleOptional('phone')">  
						</div>  
						  
						<div class="mt-4">  
							<label class="block text-gray-700 dark:text-white mb-1" for="address" id="noteLabel">  
								Note <span class="text-gray-500">(Optional)</span>  
							</label>  
							<input wire:model='notes' class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="address" type="text" oninput="toggleOptional('note')">  
						</div>  
						  
						<script>  
						function toggleOptional(field) {  
							if (field === 'phone') {  
								const phoneInput = document.getElementById('phone');  
								const phoneLabel = document.getElementById('phoneLabel');  
								if (phoneInput.value) {  
									phoneLabel.innerHTML = 'No HP';  
								} else {  
									phoneLabel.innerHTML = 'No HP <span class="text-gray-500">(Optional)</span>';  
								}  
							} else if (field === 'note') {  
								const noteInput = document.getElementById('address');  
								const noteLabel = document.getElementById('noteLabel');  
								if (noteInput.value) {  
									noteLabel.innerHTML = 'Note';  
								} else {  
									noteLabel.innerHTML = 'Note <span class="text-gray-500">(Optional)</span>';  
								}  
							}  
						}  
						</script>  
						
						<div class="grid grid-cols-2 gap-4 mt-4">
							
						</div>
					</div>
					<div class="text-lg font-semibold mb-4">
						Select Payment Method
					</div>
					<ul class="grid w-full gap-6 md:grid-cols-2">
						<li>
							<input wire:model='payment_method' class="hidden peer" id="hosting-small" required="" type="radio" value="cod" />
							<label class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" for="hosting-small">
								<div class="block">
									<div class="w-full text-lg font-semibold">
										Bayar di Kasir
									</div>
								</div>
								<svg aria-hidden="true" class="w-5 h-5 ms-3 rtl:rotate-180" fill="none" viewbox="0 0 14 10" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
									</path>
								</svg>
							</label>
						</li>
						<li>
							<input wire:model='payment_method' class="hidden peer" id="hosting-big" type="radio" value="qris">
							<label class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" for="hosting-big">
								<div class="block">
									<div class="w-full text-lg font-semibold">
										QRIS/VA
									</div>
								</div>
								<svg aria-hidden="true" class="w-5 h-5 ms-3 rtl:rotate-180" fill="none" viewbox="0 0 14 10" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
									</path>
								</svg>
							</label>
							</input>
						</li>
					</ul>

					@error('payment_method')
									<div class="text-red-400 text-sm">{{$message}}</div>
								@enderror
				</div>
				<!-- End Card -->
			</div>
			<div class="md:col-span-12 lg:col-span-4 col-span-12">
				<div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
					<div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
						ORDER SUMMARY
					</div>
					<div class="flex justify-between mb-2 font-bold">
						<span>
							Subtotal
						</span>
						<span>
							{{Number::currency($grand_total, 'IDR')}}
						</span>
					</div>
					<div class="flex justify-between mb-2 font-bold">
						<span>
							Taxes
						</span>
						<span>
							{{Number::currency(0, 'IDR')}}
						</span>
					</div>
	
					<hr class="bg-slate-400 my-4 h-1 rounded">
					<div class="flex justify-between mb-2 font-bold">
						<span>
							Grand Total
						</span>
						<span>
							{{Number::currency($grand_total, 'IDR')}}
						</span>
					</div>
					</hr>
				</div>
				<button wire:click="placeOrder" class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600">  
					Place Order  
				</button>  
				<div class="bg-white mt-4 rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
					<div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
						BASKET SUMMARY
					</div>
					<ul class="divide-y divide-gray-200 dark:divide-gray-700" role="list">
						
						@foreach ($cart_items as $ci)
						<li class="py-3 sm:py-4">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<img alt="{{$ci['name']}}" class="w-12 h-12 rounded-full" src="{{url('storage', $ci['image'])}}">
									</img>
								</div>
								<div class="flex-1 min-w-0 ms-4">
									<p class="text-sm font-medium text-gray-900 truncate dark:text-white">
										{{$ci['name']}}
									</p>
									<p class="text-sm text-gray-500 truncate dark:text-gray-400">
										Quantity: {{$ci['quantity']}}
									</p>
								</div>
								<div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
									{{Number::currency($ci['total_amount'], 'IDR')}}
								</div>
							</div>
						</li>
						@endforeach
						
					</ul>
				</div>
			</div>
		</div>
	</form>
</div>

<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>      
<script>      
    document.addEventListener('livewire:load', function () {    
        Livewire.on('openMidtrans', (data) => {    
            snap.pay('{{$snapToken}}', {      
                onSuccess: function(result) {      
                    console.log('payment success', result);
                    // Handle success (e.g., redirect to success page)    
                },      
                onPending: function(result) {      
                    console.log('waiting for payment', result);      
                    // Handle pending (e.g., notify user)    
                },      
                onError: function(result) {      
                    console.log('payment failed', result);      
                    // Handle error (e.g., notify user)    
                }      
            });      
        });    
    });    
</script>  