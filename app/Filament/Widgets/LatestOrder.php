<?php  
  
namespace App\Filament\Widgets;  
  
use App\Models\Order;  
use Filament\Support\Enums\Alignment;  
use Filament\Tables;  
use Filament\Tables\Table;  
use Filament\Widgets\TableWidget as BaseWidget;  
  
class LatestOrder extends BaseWidget  
{  
    protected static ?int $sort = 3;  
  
    protected int | string | array $columnSpan = 'full';  
  
    public function table(Table $table): Table  
    {  
        return $table  
            ->query(  
                Order::with(['orderDetails', 'user'])->latest()->take(10) // Ambil relasi user  
            )  
            ->columns([  
                Tables\Columns\TextColumn::make('created_at')  
                    ->dateTime()  
                    ->sortable()  
                    ->label('Order Date'),  
  
                Tables\Columns\TextColumn::make('user.name') // Ambil nama dari relasi user  
                    ->searchable()  
                    ->label('Customer Name'),  
  
                Tables\Columns\TextColumn::make('order_details_count')  
                    ->counts('orderDetails')  
                    ->label('Total Products'),  
  
                Tables\Columns\TextColumn::make('total_price')  
                    ->numeric(thousandsSeparator: '.')  
                    ->prefix('Rp. ')  
                    ->sortable()  
                    ->alignment(Alignment::End),  
  
                Tables\Columns\TextColumn::make('note')  
                    ->label('Note')  
                    ->searchable()  
                    ->wrap()  
                    ->default('Tidak ada catatan'),  
                Tables\Columns\TextColumn::make('status')  
                    ->label('Status')  
                    ->badge()  
                    ->color(fn (string $state): string => match ($state) {  
                        'new' => 'primary',  
                        'processing' => 'warning',  
                        'canceled' => 'danger',  
                        default => 'gray',  
                    }),  
            ]);  
    }  
}  
