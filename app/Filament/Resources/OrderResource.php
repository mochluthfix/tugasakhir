<?php  
  
namespace App\Filament\Resources;  
  
use App\Enums\OrderGender; // Pastikan enum ini ada  
use App\Filament\Resources\OrderResource\Pages;  
use App\Filament\Resources\OrderResource\RelationManagers;  
use App\Models\Order;  
use App\Models\PaymentMethod;  
use App\Models\Product;  
use Filament\Forms;  
use Filament\Forms\Form;  
use Filament\Forms\Get;  
use Filament\Forms\Set;  
use Filament\Notifications\Notification;  
use Filament\Resources\Resource;  
use Filament\Support\Enums\Alignment;  
use Filament\Tables;  
use Filament\Tables\Table;  
use Illuminate\Database\Eloquent\Builder;  
  
class OrderResource extends Resource  
{  
    protected static ?string $model = Order::class;  
  
    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';  
  
    protected static ?string $navigationGroup = 'Transaction';  
  
    public static function getEloquentQuery(): Builder  
    {  
        return parent::getEloquentQuery()->with(['orderDetails.product']);  
    }  
  
    public static function form(Form $form): Form  
    {  
        return $form  
            ->schema([  
                Forms\Components\Section::make('User Information')  
                    ->description('Information about the user / buyer.')  
                    ->icon('heroicon-s-user')  
                    ->columns(2)  
                    ->schema([  
                        Forms\Components\TextInput::make('name')  
                            ->required()  
                            ->maxLength(255)  
                            ->prefixIcon('heroicon-s-user'),  
  
                        Forms\Components\TextInput::make('phone')  
                            ->tel()  
                            ->maxLength(20)  
                            ->prefixIcon('heroicon-s-phone'),  
  
                        Forms\Components\TextInput::make('nomeja')  
                            ->nullable()  
                            ->maxLength(255)  
                            ->prefixIcon('heroicon-s-code-bracket-square'),  
                    ]),  
                Forms\Components\Section::make('Ordered Product')  
                    ->description('List of products that ordered by user.')  
                    ->icon('heroicon-s-squares-2x2')  
                    ->schema([  
                        Forms\Components\Repeater::make('orderDetails')  
                            ->relationship()  
                            ->live()  
                            ->afterStateUpdated(function (Set $set, Get $get) {  
                                self::updateOrderTotalPrice($set, $get);  
                            })  
                            ->columns([  
                                'md' => 12  
                            ])  
                            ->schema([  
                                Forms\Components\Select::make('product_id')  
                                    ->relationship('product', 'name', fn(Builder $query) => $query->where('stock', '!=', 0)->where('is_active', true))  
                                    ->searchable()  
                                    ->preload()  
                                    ->columnSpan(['md' => 4])  
                                    ->live(onBlur: true)  
                                    ->afterStateHydrated(function ($state, Set $set, Get $get) {  
                                        $product = Product::query()->find($state);  
  
                                        if ($product) {  
                                            $set('stock', $product->stock);  
                                            $set('price', $product->price);  
                                        }  
                                    })  
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {  
                                        $product = Product::query()->find($state);  
                                        $set('stock', $product->stock);  
                                        $set('price', $product->price);  
                                    })  
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),  
                                Forms\Components\TextInput::make('stock')  
                                    ->label('Stock')  
                                    ->default(0)  
                                    ->readOnly()  
                                    ->columnSpan(['md' => 1]),  
                                Forms\Components\TextInput::make('price')  
                                    ->label('Unit Price')  
                                    ->default(0)  
                                    ->readOnly()  
                                    ->columnSpan(['md' => 3]),  
                                Forms\Components\TextInput::make('quantity')  
                                    ->numeric()  
                                    ->required()  
                                    ->default(0)  
                                    ->minValue(0)  
                                    ->columnSpan(['md' => 1])  
                                    ->live()  
                                    ->afterStateHydrated(function ($state, Set $set, Get $get) {  
                                        $productPrice = $get('price') ?? 0;  
                                        $productSubtotal = $state * $productPrice;  
  
                                        $set('subtotal', $productSubtotal);  
                                    })  
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {  
                                        if ($state > $get('stock')) {  
                                            $set('quantity', $get('stock'));  
                                            Notification::make()  
                                                ->title('Stok Tidak Cukup')  
                                                ->body('Jumlah yang Anda pesan melebihi stok tersedia.')  
                                                ->warning()  
                                                ->send();  
                                        }  
  
                                        $productPrice = $get('price') ?? 0;  
                                        $productSubtotal = $state * $productPrice;  
  
                                        $set('subtotal', $productSubtotal);  
                                    }),  
                                Forms\Components\TextInput::make('subtotal')  
                                    ->label('Subtotal')  
                                    ->default(0)  
                                    ->readOnly()  
                                    ->columnSpan(['md' => 3]),  
                            ])  
                    ]),  
                Forms\Components\Section::make('Additional Information')  
                    ->description('Informasi tambahan yang diminta oleh pengguna.')  
                    ->icon('heroicon-s-list-bullet')  
                    ->schema([  
                        Forms\Components\Textarea::make('note')  
                            ->columnSpanFull()  
                            ->rows(4),  
                    ]),  
                Forms\Components\Section::make('Payment Information')  
                    ->description('Informasi pembayaran pesanan.')  
                    ->icon('heroicon-s-credit-card')  
                    ->columns(2)  
                    ->schema([  
                        Forms\Components\TextInput::make('total_price')  
                            ->required()  
                            ->numeric()  
                            ->default(0)  
                            ->prefix('Rp')  
                            ->readOnly(),  
                        
                        Forms\Components\Select::make('status')  
                            ->options([  
                                'new' => 'Baru',  
                                'processing' => 'Sedang Diproses',  
                                'canceled' => 'Dibatalkan',  
                            ])  
                            ->default('new')  
                            ->required(),  
                    ]),  
            ]);  
    }  
  
    public static function table(Table $table): Table  
    {  
        return $table  
            ->defaultSort('created_at', 'desc')  
            ->columns([  
                Tables\Columns\TextColumn::make('created_at')  
                    ->dateTime()  
                    ->sortable()  
                    ->label('Tanggal Pesanan'),  
                Tables\Columns\TextColumn::make('name')  
                    ->searchable(),  
                Tables\Columns\TextColumn::make('order_details_count')  
                    ->counts('orderDetails')  
                    ->label('Total Produk'),  
                Tables\Columns\TextColumn::make('total_price')  
                    ->numeric(thousandsSeparator: '.')  
                    ->prefix('Rp. ')  
                    ->sortable()  
                    ->alignment(Alignment::End),  
                Tables\Columns\TextColumn::make('status')  
                    ->label('Status')  
                    ->formatStateUsing(fn ($state) => match ($state) {  
                        'new' => 'Baru',  
                        'processing' => 'Sedang Diproses',  
                        'canceled' => 'Dibatalkan',  
                    }),  
                Tables\Columns\TextColumn::make('note')  
                    ->label('Catatan')  
                    ->sortable(),  
                Tables\Columns\TextColumn::make('updated_at')  
                    ->dateTime()  
                    ->sortable()  
                    ->toggleable(isToggledHiddenByDefault: true),  
            ])  
            ->filters([  
                //   
            ])  
            ->actions([  
                Tables\Actions\ViewAction::make()->color('primary'),  
                Tables\Actions\EditAction::make()->color('warning'),  
                Tables\Actions\DeleteAction::make(),  
                Tables\Actions\Action::make('approve')  
                    ->label('Setujui Pembayaran')  
                    ->action(function (Order $record) {  
                        $record->update([  
                            'status' => 'processing',  
                        ]);  
                        Notification::make()  
                            ->title('Pembayaran Disetujui')  
                            ->success()  
                            ->send();  
                    })  
                    ->requiresConfirmation()  
                    ->color('success'),  
            ])  
            ->bulkActions([  
                Tables\Actions\BulkActionGroup::make([  
                    Tables\Actions\DeleteBulkAction::make(),  
                ]),  
            ]);  
    }  
  
    public static function getRelations(): array  
    {  
        return [  
            //   
        ];  
    }  
  
    public static function getPages(): array  
    {  
        return [  
            'index' => Pages\ListOrders::route('/'),  
            'create' => Pages\CreateOrder::route('/create'),  
            'view' => Pages\ViewOrder::route('/{record}'),  
            'edit' => Pages\EditOrder::route('/{record}/edit'),  
        ];  
    }  
  
    protected static function updateOrderTotalPrice(Set $set, Get $get): void  
    {  
        $selectedProducts = collect($get('orderDetails'))->filter(fn($item) => !empty($item['product_id']));  
  
        $unitPrices = Product::query()->find($selectedProducts->pluck('product_id'))->pluck('price', 'id');  
  
        $totalPrice = $selectedProducts->reduce(function ($total, $product) use ($unitPrices) {  
            return $total + ($unitPrices[$product['product_id']] * $product['quantity']);  
        }, 0);  
  
        $set('total_price', $totalPrice);  
    }  
}  
