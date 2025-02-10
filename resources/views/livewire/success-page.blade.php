<section class="flex items-center font-poppins dark:bg-gray-800 ">  
  <div class="justify-center flex-1 max-w-6xl px-4 py-4 mx-auto bg-white border rounded-md dark:border-gray-900 dark:bg-gray-900 md:py-10 md:px-10">  
      <div>  
          <h1 class="px-4 mb-8 text-2xl font-semibold tracking-wide text-gray-700 dark:text-gray-300 ">  
              Terima kasih telah memesan, selesaikan pembayaran anda di kasir agar pesanan siap diproses.  
          </h1>  
          <div class="flex border-b border-gray-200 dark:border-gray-700 items-stretch justify-start w-full h-full px-4 mb-8 md:flex-row xl:flex-col md:space-x-6 lg:space-x-8 xl:space-x-0">  
              <div class="flex items-start justify-start flex-shrink-0">  
                  <div class="flex items-center justify-center w-full pb-6 space-x-4 md:justify-start">  
                      <div class="flex flex-col items-start justify-start space-y-2">  
                          <p class="text-lg font-semibold leading-4 text-left text-gray-800 dark:text-gray-400">  
                              {{ $order->name }}  
                          </p>  
                          <p class="text-sm leading-4 text-gray-600 dark:text-gray-400">{{ $order->phone }}</p>  
                          <p class="text-sm leading-4 text-gray-600 dark:text-gray-400">{{ $order->nomeja }}</p>  
                      </div>  
                  </div>  
              </div>  
          </div>  
          <div class="flex flex-wrap items-center pb-4 mb-10 border-b border-gray-200 dark:border-gray-700">  
              <div class="w-full px-4 mb-4 md:w-1/4">  
                  <p class="mb-2 text-sm leading-5 text-gray-600 dark:text-gray-400 ">  
                      Order Number:  
                  </p>  
                  <p class="text-base font-semibold leading-4 text-gray-800 dark:text-gray-400">  
                      {{ $order->id }}  
                  </p>  
              </div>  
              <div class="w-full px-4 mb-4 md:w-1/4">  
                  <p class="mb-2 text-sm leading-5 text-gray-600 dark:text-gray-400 ">  
                      Date:  
                  </p>  
                  <p class="text-base font-semibold leading-4 text-gray-800 dark:text-gray-400">  
                      {{ $order->created_at->format('d-m-Y') }}  
                  </p>  
              </div>  
              <div class="w-full px-4 mb-4 md:w-1/4">  
                  <p class="mb-2 text-sm font-medium leading-5 text-gray-800 dark:text-gray-400 ">  
                      Total:  
                  </p>  
                  <p class="text-base font-semibold leading-4 text-blue-600 dark:text-gray-400">  
                      {{ Number::currency($order->total_price, 'Rp') }}  
                  </p>  
              </div>  
              <div class="w-full px-4 mb-4 md:w-1/4">  
                  <p class="mb-2 text-sm leading-5 text-gray-600 dark:text-gray-400 ">  
                      Payment Method:  
                  </p>  
                  <p class="text-base font-semibold leading-4 text-gray-800 dark:text-gray-400 ">  
                      Bayar di Kasir
                  </p>  
              </div>  
          </div>  
          <div class="px-4 mb-10">  
              <div class="flex flex-col items-stretch justify-center w-full space-y-4 md:flex-row md:space-y-0 md:space-x-8">  
                  <div class="flex flex-col w-full space-y-6 ">  
                      <h2 class="mb-2 text-xl font-semibold text-gray-700 dark:text-gray-400">Order details</h2>  
                      <div class="flex flex-col items-center justify-center w-full pb-4 space-y-4 border-b border-gray-200 dark:border-gray-700">  
                          @foreach ($order->orderDetails as $detail)  
                              <div class="flex justify-between w-full">  
                                  <p class="text-base leading-4 text-gray-800 dark:text-gray-400">{{ $detail->product->name }}</p>  
                                  <p class="text-base leading-4 text-gray-600 dark:text-gray-400">{{Number::currency($order->total_price, 'Rp.')}}</p>  
                              </div>  
                          @endforeach  
                      </div>  
                      <div class="flex items-center justify-between w-full">  
                          <p class="text-base font-semibold leading-4 text-gray-800 dark:text-gray-400">Total</p>  
                          <p class="text-base font-semibold leading-4 text-gray-600 dark:text-gray-400">{{ Number::currency($order->total_price, 'Rp.') }}</p>  
                      </div>  
                  </div>  
              </div>  
          </div>  
          <div class="flex items-center justify-start gap-4 px-4 mt-6 ">  
              <a href="/products" class="w-full text-center px-4 py-2 text-blue-500 border border-blue-500 rounded-md md:w-auto hover:text-white hover:bg-blue-600 dark:border-gray-700 dark:hover:bg-gray-700 dark:text-gray-300">  
                  Go back shopping  
              </a>  
          </div>  
      </div>  
  </div>  
</section>  
