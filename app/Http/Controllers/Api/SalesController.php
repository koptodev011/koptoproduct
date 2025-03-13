<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
// use App\Models\Salespaymenttype; // Removed duplicate import
use App\Models\ProductSale;
use App\Models\Salefilterindex;
use App\Models\PaymentType;
use App\Models\Paymentin;
use App\Models\Party;
use App\Models\Tenant;
use App\Models\TenantUnit;
use App\Models\PartyType;
use App\Models\PartyCategory;
use App\Models\Product;
use App\Models\Producttaxrate;
use App\Models\Salespaymenttype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class SalesController extends Controller
{

    public function addSaleInvoice(Request $request)
{   
    $validator = Validator::make($request->all(), [
        "sale_type" => "nullable|numeric",
        "party_id" => "nullable|integer",
        "billing_name" => "nullable|string",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        "sales_document" => "nullable",
        "received_amount" => "nullable|integer",
        "payment_type_id" => "required|integer",
        "invoice_no" => 'required|integer',
        "invoice_date" => "nullable|string",
        "due_date" => "nullable|string",
        "reference_number" => "nullable|integer",
        "billing_address" => "nullable|string",
        // Items array validation
        "items" => "required|array",
        "items.*.product_id" => "required|integer|exists:products,id",
        "items.*.quantity" => "required|integer",
        "items.*.price_per_unit" => "required", 
        "items.*.discount_percentage" => "nullable|integer", 
        "items.*.discount_amount" => "nullable|integer",
        "items.*.tax_percentage" => "nullable|integer",
        "items.*.tax_amount" => "nullable|integer",
        "items.*.unit_id" => "required|integer",
        "items.*.product_amount" => "required",
        "total_amount" => "required",
    ]);   
     
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

 
    $user = auth()->user(); 
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
    $tenants = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();
      
    $sale = new Sale();
    $sale->sale_type = $request->sale_type;
    $sale->party_id = $request->party_id;
    $sale->billing_name = $request->billing_name;
    $sale->phone_number = $request->phone_number;
    $sale->po_number = $request->po_number;
    $sale->po_date = $request->po_date;
    $sale->received_amount = $request->received_amount;
    $sale->payment_type_id = $request->payment_type_id;
    $sale->sale_description = $request->sale_description;
    $sale->status = "sale";
    $sale->invoice_no = $request->invoice_no;
    $sale->invoice_date = $request->invoice_date;
    $sale->due_date = $request->due_date;
    $sale->reference_no = $request->reference_no;
    $sale->total_amount = $request->total_amount;
    $sale->tenant_unit_id = $tenants->id;
    $sale->billing_address = $request->billing_address;
    if($request->sale_type == 1){
        $sale->sales_status = "Unpaid";
    }else{
        $sale->sales_status = "Paid";
    }
    if ($request->hasFile('sale_image')) {
        $image = $request->file('sale_image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('sales_images', $imageName, 'public'); 
        $sale->sale_image = $imagePath;
    }
    if ($request->hasFile('sales_document')) {
        $document = $request->file('sales_document');
        $documentName = time() . '_' . $document->getClientOriginalName();
        $documentPath = $document->storeAs('sales_documents', $documentName, 'public'); 
        $sale->sales_document = $documentPath;
    }
    
    $sale->save();
    $products = []; // Array to store product details

    foreach ($request->items as $item) {
        $productSale = new ProductSale();
        $productSale->product_id = $item['product_id'];
        $productSale->quantity = $item['quantity'];
        $productSale->amount = $item['product_amount'];
        $productSale->unit_id = $item['unit_id'];
        $productSale->priceperunit = $item['price_per_unit'];
        $productSale->discount_percentage = $item['discount_percentage'] ?? null;
        $productSale->discount_amount = $item['discount_amount'] ?? null;
        $productSale->tax_percentage = $item['tax_percentage'] ?? null;
        $productSale->tax_amount = $item['tax_amount'] ?? null;
        $productSale->sale_id = $sale->id;
        $productSale->save();
        
        $searchMainTenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
        
        $product = Product::where('id', $item['product_id']) // Fixed reference
            ->where('tenant_id', $searchMainTenant->id)
            ->with([
                'productUnitConversion',
                'pricing',
                'wholesalePrice',
                'stock',
                'onlineStore',
                'images',
                'purchasePrice'
            ])
            ->first();   
            $payment_method = Salespaymenttype::where('id', $request->payment_type_id)->first();

            if ($product->productUnitConversion->product_base_unit_id == $productSale->unit_id) {
                // dd("This is primary unit");
              $productStock = $product->stock;
              $productunitconversion = $product->productUnitConversion;
              if($productStock){
                $productstock = $productStock->product_stock - $item['quantity'];
                $productStock->product_stock = $productstock;
                $secondarystock = $productunitconversion->conversion_rate * $item['quantity'];
                $productStock->secondaryunit_stock_value = $productStock->secondaryunit_stock_value - $secondarystock;
                $productStock->save();
              }
            }
            if ($product->productUnitConversion->product_secondary_unit_id == $productSale->unit_id) {
                // dd("This is secondary unit");
                $productStock = $product->stock; 
                if ($productStock) {
                    $productStock->secondaryunit_stock_value -= $productSale->quantity;
                    if ($productStock->secondaryunit_stock_value < 0) {
                        return response()->json([
                            'message' => 'Not enough stock available for this product',
                            'product_id' => $product->id
                        ], 400);
                    }
            
                    $productStock->save();

                    $searchbaseunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_base_unit_id)->sum('quantity');
                    $quantitybaseunitproducts = $searchbaseunitproducts * $product->productUnitConversion->conversion_rate;
                    
                    $searchsecondaryunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_secondary_unit_id)->sum('quantity');
                    $totalsalesquantity = $quantitybaseunitproducts + $searchsecondaryunitproducts;
        
                    $buyingstock = $totalsalesquantity/$product->productUnitConversion->conversion_rate; 
                    if($buyingstock<1){
                       continue;
                    }
                    else{
                        $productstock = ceil($product->stock->previous_stock - $buyingstock);
                        $productStock->product_stock = $productstock;
                        $productStock->save();
                    }
                }
            }
            
        
        if ($product) {
            $products[] = $product;
        }
    }
    return response()->json([
        "invoice_number" => $sale->invoice_no,
        "date" => $sale->invoice_date,
        "due_date" => $sale->due_date,
        "reference_no" => $sale->reference_no,
        "billing_name" => $sale->billing_name,
        "phone_number" => $sale->phone_number,
        "address" => $sale->billing_address ?? "N/A",
        "items" => collect($request->items)->map(function ($item, $index) {
            $product = Product::find($item['product_id']);
            $taxes = $product ? ProductTaxRate::where('id', $product->tax_id)->get() : collect([]);
            
            return [
                "no" => $index + 1,
                "product_name" => $product ? $product->product_name : "N/A",
                "description" => $product ? $product->description : "N/A",
                "quantity" => $item['quantity'],
                "unit_price" => $item['price_per_unit'],
                "total" => $item['product_amount'],
                "tax" => $taxes->map(function ($tax) {
                    return [
                        "tax_name" => $tax->product_tax_name,
                        "tax_percentage" => $tax->product_tax_rate
                    ];
                })
            ];
        }),
        "total_amount" => $sale->total_amount,
        "payment_method" => $payment_method->sales_payment_type
    ], 200);
     
}








public function getSalesData(Request $request)
{
    $user = auth()->user();

    // Validate the input
    $validator = Validator::make($request->all(), [
        'salefilter' => "nullable",
        "startdate" => "required_if:salefilter,Custom|date_format:d/m/Y",
        "enddate" => "required_if:salefilter,Custom|date_format:d/m/Y|after_or_equal:startdate",
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    try {
        switch ($request->salefilter) {
            case "This month":
                $startdate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                break;

            case "Last month":
                $startdate = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;

            case "Last quarter":
                $currentMonth = \Carbon\Carbon::now()->month;
                $currentQuarter = ceil($currentMonth / 3);
                $lastQuarter = $currentQuarter - 1;
                $year = \Carbon\Carbon::now()->year;

                if ($lastQuarter == 0) {
                    $lastQuarter = 4;
                    $year--;
                }

                $startdate = \Carbon\Carbon::createFromDate($year, ($lastQuarter - 1) * 3 + 1, 1)->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromDate($year, $lastQuarter * 3, 1)->endOfMonth()->format('Y-m-d');
                break;

            case "This year":
                $startdate = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
                break;

            case "Custom":
                $startdate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->startdate)->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->enddate)->format('Y-m-d');
                break;

            default:
                return response()->json([
                    'message' => 'Invalid filter provided.',
                ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Date conversion failed',
            'error' => $e->getMessage(),
        ], 400);
    }

    // Retrieve active tenant for the authenticated user
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    if (!$maintenant) {
        return response()->json([
            'message' => 'No active tenant found for this user.',
        ], 404);
    }

    // Get the first active TenantUnit
    $tenantUnit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city'])
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();

    if (!$tenantUnit) {
        return response()->json([
            'message' => 'No active tenant unit found.',
        ], 404);
    }

    // Fetch sales data with product details and product count
    $salesQuery = Sale::with(['productSales'])  // Include product details
        ->withCount('productSales')  // Count number of productSales per sale
        ->where('tenant_unit_id', $tenantUnit->id)
        ->where('status', 'sale')
        ->whereBetween('created_at', [$startdate, $enddate]);

    $totalAmount = $salesQuery->sum('total_amount');
    $receivedAmount = $salesQuery->sum('received_amount');
    $paidAmount = $totalAmount - $receivedAmount;
    
    if (!empty($request->party_id)) {
        $salesQuery->where('party_id', $request->party_id);
    }

    $sales = $salesQuery->get();

    // Count total number of products sold
    $totalProductsSold = $sales->sum('product_sales_count');

    if ($sales->isEmpty()) {
        return response()->json([
            'message' => 'No data found for the given date range',
            'total_amount' => $totalAmount,
            'received_amount' => $receivedAmount,
            'paid_amount' => $paidAmount,
            'total_products_sold' => $totalProductsSold,
            'data' => [],
        ], 200);
    }

    return response()->json([
        'message' => 'Sales data retrieved successfully',
        'total_amount' => $totalAmount,
        'received_amount' => $receivedAmount,
        'paid_amount' => $paidAmount,
        'data' => $sales,
    ], 200);
}




//Incomplete code 

public function updateSaleInvoice(Request $request)
{
    $validator = Validator::make($request->all(), [
        "sale_id" => "required|exists:sales,id",
        "party_id" => "nullable|integer",
        "billing_name" => "nullable|string",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        "sales_document" => "nullable",
        "received_amount" => "nullable|integer",
        "payment_type_id" => "nullable|integer",
        "invoice_no" => "required|integer",
        "invoice_date" => "nullable|string",
        "due_date" => "nullable|string",
        "reference_number" => "nullable|integer",
        "billing_address" => "nullable|string",
        
        // Item validation
        "items" => "nullable|array",
        "items.*.id" => "nullable|integer|exists:productsales,id",
        "items.*.product_id" => "nullable|integer|exists:products,id",
        "items.*.quantity" => "nullable|integer|min:1",
        "items.*.price_per_unit" => "nullable|numeric|min:0",
        "items.*.discount_percentage" => "nullable|integer|min:0",
        "items.*.discount_amount" => "nullable|numeric|min:0",
        "items.*.tax_percentage" => "nullable|integer|min:0",
        "items.*.tax_amount" => "nullable|numeric|min:0",
        "items.*.unit_id" => "nullable|integer",
        "items.*.product_amount" => "nullable|numeric|min:0",
        "total_amount" => "nullable|numeric|min:0"
    ]);

    if ($validator->fails()) {
        return response()->json(["errors" => $validator->errors()], 422);
    }

    // Find sale record
    $sale = Sale::with('productSales')->find($request->sale_id);
    if (!$sale) {
        return response()->json(["message" => "Sale record not found."], 404);
    }
    

    // Update sale details
    $sale->update($request->only([
        "party_id", "billing_name", "billing_address", "phone_number", 
        "po_number", "po_date", "sale_description", "received_amount", 
        "payment_type_id", "invoice_no", "invoice_date", "due_date", 
        "reference_number"
    ]));

    if ($request->hasFile('sale_image')) {
        $imagePath = $request->file('sale_image')->store('sale_images', 'public');
        $sale->update(['sale_image' => $imagePath]);
    }
    
    $existingProductSales = $sale->productSales->keyBy('id');
   
    if ($request->has('items')) {
        foreach ($request->items as $item) {
            if (!empty($item['id']) && $existingProductSales->has($item['id'])) {
                $existingProduct = $existingProductSales[$item['id']];  
                if ($existingProduct->quantity != $item['quantity']) {
                    if( $item['quantity']>$existingProduct->quantity){
                        $product = Product::where('id', $item['product_id'])
                        ->with([
                            'productUnitConversion',
                            'pricing',
                            'wholesalePrice',
                            'stock',
                            'onlineStore',
                            'images',
                            'purchasePrice'
                        ])
                        ->first();  
                      

                        if ($product->productUnitConversion->product_base_unit_id == $existingProduct->unit_id && $product->productUnitConversion->product_base_unit_id == $item['unit_id']) {
                         dd("This is the base unit");
                          $productStock = $product->stock;
                          $productunitconversion = $product->productUnitConversion;
                          if($productStock){
                            $productstock = $productStock->product_stock - $item['quantity'];
                            $productStock->product_stock = $productstock;
                            //Secondary stock management
                            $secondarystock = $productunitconversion->conversion_rate * $item['quantity'];
                            $productStock->secondaryunit_stock_value = $productStock->secondaryunit_stock_value - $secondarystock;
                            $productStock->save();
                          }
                        }
                        
                        if ($product->productUnitConversion->product_secondary_unit_id == $existingProduct->unit_id && $product->productUnitConversion->product_secondary_unit_id == $item['unit_id']) {

                            $productStock = $product->stock; 
                            if ($productStock) {
                                $stockneedtoadd = $item['quantity']-$existingProduct->quantity;
                                $productStock->secondaryunit_stock_value =  $productStock->secondaryunit_stock_value - $stockneedtoadd;
                                if ($productStock->secondaryunit_stock_value < 0) {
                                    return response()->json([
                                        'message' => 'Not enough stock available for this product',
                                        'product_id' => $product->id
                                    ], 400);
                                }
                        
                                // $productStock->save();
                                $existingProduct->quantity = $existingProduct->quantity + $stockneedtoadd;
                                $existingProduct->save();

                                $searchbaseunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_base_unit_id)->sum('quantity');
                                $quantitybaseunitproducts = $searchbaseunitproducts * $product->productUnitConversion->conversion_rate;
                                $searchsecondaryunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_secondary_unit_id)->sum('quantity');
                                $totalsalesquantity = $quantitybaseunitproducts + $searchsecondaryunitproducts;
                    
                                $buyingstock = $totalsalesquantity/$product->productUnitConversion->conversion_rate; 
                                if($buyingstock<1){
                                   continue;
                                }
                                else{
                                    $productstock = ceil($product->stock->previous_stock - $buyingstock);
                                    $productStock->product_stock = $productstock;
                                    $productStock->save();
                                }
                               
                            }
                        }
                    }
                }
                
                $existingProduct->update([
                    "product_id" => $item['product_id'],
                    "price_per_unit" => $item['price_per_unit'],
                    "discount_percentage" => $item['discount_percentage'] ?? 0,
                    "discount_amount" => $item['discount_amount'] ?? 0,
                    "tax_percentage" => $item['tax_percentage'] ?? 0,
                    "tax_amount" => $item['tax_amount'] ?? 0,
                    "unit_id" => $item['unit_id'],
                    "amount" => $item['product_amount']
                ]);
            } else {
                $sale->productSales()->create([
                    "product_id" => $item['product_id'],
                    "quantity" => $item['quantity'],
                    "priceperunit" => $item['price_per_unit'],
                    "discount_percentage" => $item['discount_percentage'] ?? 0,
                    "discount_amount" => $item['discount_amount'] ?? 0,
                    "tax_percentage" => $item['tax_percentage'] ?? 0,
                    "tax_amount" => $item['tax_amount'] ?? 0,
                    "unit_id" => $item['unit_id'],
                    "amount" => $item['product_amount'],
                    "sale_id" => $sale->id
                ]);
                $product = Product::where('id', $item['product_id'])
                ->with([
                    'productUnitConversion',
                    'pricing',
                    'wholesalePrice',
                    'stock',
                    'onlineStore',
                    'images',
                    'purchasePrice'
                ])
                ->first(); 

                if ($product->productUnitConversion->product_base_unit_id == $item['unit_id']) {
                    dd("This is base unit ");

                  $productStock = $product->stock;
                  $productunitconversion = $product->productUnitConversion;
                  if($productStock){
                    $productstock = $productStock->product_stock - $item['quantity'];
                    $productStock->product_stock = $productstock;
                    //Secondary stock management
                    $secondarystock = $productunitconversion->conversion_rate * $item['quantity'];
                    $productStock->secondaryunit_stock_value = $productStock->secondaryunit_stock_value - $secondarystock;
                    $productStock->save();
                  }
                }
                
                if ($product->productUnitConversion->product_secondary_unit_id == $item['unit_id']) {
                    $productStock = $product->stock; 
                    if ($productStock) {
                        $productStock->secondaryunit_stock_value -= $item['quantity'];
                        if ($productStock->secondaryunit_stock_value < 0) {
                            return response()->json([
                                'message' => 'Not enough stock available for this product',
                                'product_id' => $product->id
                            ], 400);
                        }
                
                      
                        $productStock->save();

                        $searchbaseunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_base_unit_id)->sum('quantity');
                        $quantitybaseunitproducts = $searchbaseunitproducts * $product->productUnitConversion->conversion_rate;
                        $searchsecondaryunitproducts = Productsale::where('product_id',$item['product_id'])->where('unit_id',$product->productUnitConversion->product_secondary_unit_id)->sum('quantity');
                        $totalsalesquantity = $quantitybaseunitproducts + $searchsecondaryunitproducts;
            
                        $buyingstock = $totalsalesquantity/$product->productUnitConversion->conversion_rate; 
                        if($buyingstock<1){
                           continue;
                        }
                        else{
                            $productstock = ceil($product->stock->previous_stock - $buyingstock);
                            $productStock->product_stock = $productstock;
                            $productStock->save();
                        }
                       
                    }
                }
            }
        }
    }

    return response()->json([
        "message" => "Sale invoice updated successfully.",
        "existing_product_sales_ids" => $existingProductSales->keys()->toArray()
    ]);
}



// public function deleteSaleInvoice($sale_id){
//     $sale = Sale::with('productSales')->find($sale_id);
//     if (!$sale) {
//         return response()->json(["message" => "Sale record not found."], 404);
//     }

//     return response()->json([
//         'data' => $sale
//     ], 200);
// }



public function deleteSaleInvoice($sale_id){
    $sale = Sale::with('productSales')->find($sale_id);
    if (!$sale) {
        return response()->json(["message" => "Sale record not found."], 404);
    }

    foreach ($sale->productSales as $productSale) {
        \Log::info('Product Sale ID: ' . $productSale->id);
        $product = Product::where('id', $productSale->product_id)
        ->with([
            'productUnitConversion',
            'pricing',
            'wholesalePrice',
            'stock',
            'onlineStore',
            'images',
            'purchasePrice'
        ])
        ->first();

        if ($product) {
            $productStock = $product->stock;

            if ($product->productUnitConversion->product_base_unit_id == $productSale->unit_id) {
                // Handle base unit stock update if needed
            }

            if ($product->productUnitConversion->product_secondary_unit_id == $productSale->unit_id) {
                $productsalequantity = $productSale->quantity;
                $productStock->secondaryunit_stock_value += $productsalequantity;
                $productStock->save();
            }

            // Delete the product sale
            $productSale->delete();

            // Update stock values
            $searchbaseunitproducts = ProductSale::where('product_id', $productSale->product_id)
                ->where('unit_id', $product->productUnitConversion->product_base_unit_id)
                ->sum('quantity');
            $quantitybaseunitproducts = $searchbaseunitproducts * $product->productUnitConversion->conversion_rate;

            $searchsecondaryunitproducts = ProductSale::where('product_id', $productSale->product_id)
                ->where('unit_id', $product->productUnitConversion->product_secondary_unit_id)
                ->sum('quantity');
            $totalsalesquantity = $quantitybaseunitproducts + $searchsecondaryunitproducts;

            $buyingstock = $totalsalesquantity / $product->productUnitConversion->conversion_rate;
            if ($buyingstock >= 1) {
                $productstock = ceil($product->stock->previous_stock - $buyingstock);
                $productStock->product_stock = $productstock;
                $productStock->save();
            }
        }
    }

    // Delete the sale
    $sale->delete();

    return response()->json([
        'message' => 'Sale and associated product sales deleted successfully.'
    ], 200);
}






    
    public function getAllSaleSearchFilters(){
       $salefilterindex = Salefilterindex::all();
       return response()->json([
        'Sale filter index' => $salefilterindex
    ], 200);
    }
    


    
    public function addSalesPaymentType(Request $request){
        $validator = Validator::make($request->all(), [
            "payment_type" => "required|string",
        ]);
        $salespaymenttype = new Salespaymenttype();
        $salespaymenttype->sales_payment_type = $request->payment_type;
        $salespaymenttype->save();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => $request->all(),
            ], 400);
        }
        return response()->json([
            'message' => 'Payment type created successfully',
            'data' => $request->all(),
        ], 200);
    }


    


  
    public function getSalesPaymentType(){
        $salespaymenttype = Salespaymenttype::all();
        return response()->json($salespaymenttype, 200);
    }






//Sales Quotation section

public function addSalesQuotation(Request $request){
    $validator = Validator::make($request->all(), [
        "party_id" => "nullable|integer",
        "billing_name" => "nullable|string",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        "sales_document" => "nullable",
        "payment_type_id" => "required|integer",
        "invoice_no" => 'required|integer',
        "invoice_date" => "nullable|string",
        "due_date" => "nullable|string",
        "reference_number" => "nullable|integer",
        "billing_address" => "nullable|string",
        // Items array validation
        "items" => "required|array",
        "items.*.product_id" => "required|integer|exists:products,id",
        "items.*.quantity" => "required|integer",
        "items.*.price_per_unit" => "required", 
        "items.*.discount_percentage" => "nullable|integer", 
        "items.*.discount_amount" => "nullable|integer",
        "items.*.tax_percentage" => "nullable|integer",
        "items.*.tax_amount" => "nullable|integer",
        "items.*.unit_id" => "required|integer",
        "items.*.product_amount" => "required",
        "total_amount" => "required",
    ]);   
    
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    $user = auth()->user(); 
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
    $tenants = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();
      
    $sale = new Sale();
    $sale->sale_type = 0;
    $sale->party_id = $request->party_id;
    $sale->billing_name = $request->billing_name;
    $sale->phone_number = $request->phone_number;
    $sale->po_number = $request->po_number;
    $sale->po_date = $request->po_date;
    $sale->received_amount = $request->received_amount;
    $sale->payment_type_id = $request->payment_type_id;
    $sale->sale_description = $request->sale_description;
    $sale->status = "Quotation Open";
    $sale->invoice_no = $request->invoice_no;
    $sale->invoice_date = $request->invoice_date;
    $sale->due_date = $request->due_date;
    $sale->reference_no = $request->reference_no;
    $sale->total_amount = $request->total_amount;
    $sale->tenant_unit_id = $tenants->id;
    $sale->billing_address = $request->billing_address;
    if($request->sale_type == 1){
        $sale->sales_status = "Unpaid";
    }else{
        $sale->sales_status = "Paid";
    }
    if ($request->hasFile('sale_image')) {
        $image = $request->file('sale_image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('sales_images', $imageName, 'public'); 
        $sale->sale_image = $imagePath;
    }
    if ($request->hasFile('sales_document')) {
        $document = $request->file('sales_document');
        $documentName = time() . '_' . $document->getClientOriginalName();
        $documentPath = $document->storeAs('sales_documents', $documentName, 'public'); 
        $sale->sales_document = $documentPath;
    }
    
    $sale->save();
    $products = []; // Array to store product details

    foreach ($request->items as $item) {
        $productSale = new ProductSale();
        $productSale->product_id = $item['product_id'];
        $productSale->quantity = $item['quantity'];
        $productSale->amount = $item['product_amount'];
        $productSale->unit_id = $item['unit_id'];
        $productSale->priceperunit = $item['price_per_unit'];
        $productSale->discount_percentage = $item['discount_percentage'] ?? null;
        $productSale->discount_amount = $item['discount_amount'] ?? null;
        $productSale->tax_percentage = $item['tax_percentage'] ?? null;
        $productSale->tax_amount = $item['tax_amount'] ?? null;
        $productSale->sale_id = $sale->id;
        $productSale->save();
       
        $product = Product::find($item['product_id']);
        if ($product) {
            $products[] = $product;
        }
    }

    return response()->json([
        "invoice_number" => $sale->invoice_no,
        "date" => $sale->invoice_date,
        "due_date" => $sale->due_date,
        "reference_no" => $sale->reference_no,
        "billing_name" => $sale->billing_name,
        "phone_number" => $sale->phone_number,
        "address" => $sale->billing_address ?? "N/A",
        "items" => collect($request->items)->map(function ($item, $index) {
            $product = Product::find($item['product_id']);
            $taxes = $product ? ProductTaxRate::where('id', $product->tax_id)->get() : collect([]);
            
            return [
                "no" => $index + 1,
                "product_name" => $product ? $product->product_name : "N/A",
                "description" => $product ? $product->description : "N/A",
                "quantity" => $item['quantity'],
                "unit_price" => $item['price_per_unit'],
                "total" => $item['product_amount'],
                "tax" => $taxes->map(function ($tax) {
                    return [
                        "tax_name" => $tax->product_tax_name,
                        "tax_percentage" => $tax->product_tax_rate
                    ];
                })
            ];
        }),
        "total_amount" => $sale->total_amount
    ], 200);
     
}







public function getSalequotationdata(Request $request){
    $user = auth()->user();
    $validator = Validator::make($request->all(), [
        'salefilter' => "nullable",
        "startdate" => "required_if:salefilter,Custom|date_format:d/m/Y",
        "enddate" => "required_if:salefilter,Custom|date_format:d/m/Y|after_or_equal:startdate",
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    try {
        switch ($request->salefilter) {
            case "This month":
                $startdate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                break;

            case "Last month":
                $startdate = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;

            case "Last quarter":
                $currentMonth = \Carbon\Carbon::now()->month;
                $currentQuarter = ceil($currentMonth / 3);
                $lastQuarter = $currentQuarter - 1;
                $year = \Carbon\Carbon::now()->year;

                if ($lastQuarter == 0) {
                    $lastQuarter = 4;
                    $year--;
                }

                $startdate = \Carbon\Carbon::createFromDate($year, ($lastQuarter - 1) * 3 + 1, 1)->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromDate($year, $lastQuarter * 3, 1)->endOfMonth()->format('Y-m-d');
                break;

            case "This year":
                $startdate = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
                break;

            case "Custom":
                $startdate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->startdate)->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->enddate)->format('Y-m-d');
                break;

            default:
                return response()->json([
                    'message' => 'Invalid filter provided.',
                ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Date conversion failed',
            'error' => $e->getMessage(),
        ], 400);
    }

    // Retrieve active tenant for the authenticated user
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    if (!$maintenant) {
        return response()->json([
            'message' => 'No active tenant found for this user.',
        ], 404);
    }

    // Get the first active TenantUnit
    $tenantUnit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city'])
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();

    if (!$tenantUnit) {
        return response()->json([
            'message' => 'No active tenant unit found.',
        ], 404);
    }

    // Fetch sales data with product details and product count
    $salesQuery = Sale::with(['productSales'])  // Include product details
        ->withCount('productSales')  // Count number of productSales per sale
        ->where('tenant_unit_id', $tenantUnit->id)
        ->where('status', 'Quotation Open')
        ->whereBetween('created_at', [$startdate, $enddate]);

    $totalAmount = $salesQuery->sum('total_amount');
    $receivedAmount = $salesQuery->sum('received_amount');
    $paidAmount = $totalAmount - $receivedAmount;
    
    if (!empty($request->party_id)) {
        $salesQuery->where('party_id', $request->party_id);
    }

    $sales = $salesQuery->get();

    // Count total number of products sold
    $totalProductsSold = $sales->sum('product_sales_count');

    if ($sales->isEmpty()) {
        return response()->json([
            'message' => 'No data found for the given date range',
            'total_amount' => $totalAmount,
            'received_amount' => $receivedAmount,
            'paid_amount' => $paidAmount,
            'total_products_sold' => $totalProductsSold,
            'data' => [],
        ], 200);
    }

    return response()->json([
        'message' => 'Sales Quotation data retrieved successfully',
        'data' => $sales,
    ], 200);
}













public function convertQuotationToSale(Request $request){
    $validator = Validator::make($request->all(), [
        "sale_id" => "required|integer",
    ]);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }
    $sale = Sale::with('productSales')->find($request->sale_id);
    foreach ($sale->productSales as $productSale) {
        $product = Product::where('id', $productSale->product_id)
        ->with([
            'productUnitConversion',
            'pricing',
            'wholesalePrice',
            'stock',
            'onlineStore',
            'images',
            'purchasePrice'
        ])
        ->first();
        if ($product) {
            if ($product->productUnitConversion->product_base_unit_id == $productSale->unit_id) {
                $productStock = $product->stock;
                $productunitconversion = $product->productUnitConversion;
                if($productStock){
                  $productstock = $productStock->product_stock - $productSale->quantity;
                  $productStock->product_stock = $productstock;
                  //Secondary stock management
                  $secondarystock = $productunitconversion->conversion_rate * $productSale->quantity;
                  $productStock->secondaryunit_stock_value = $productStock->secondaryunit_stock_value - $secondarystock;
                  $productStock->save();
                }
            }
            
            if ($product->productUnitConversion->product_secondary_unit_id == $productSale->unit_id) {
                $productStock = $product->stock; 
                if ($productStock) {
                    $productStock->secondaryunit_stock_value -= $productSale->quantity;
                    if ($productStock->secondaryunit_stock_value < 0) {
                        return response()->json([
                            'message' => 'Not enough stock available for this product',
                            'product_id' => $product->id
                        ], 400);
                    }
            
                    $productStock->save();

                    $searchbaseunitproducts = Productsale::where('product_id',$productSale->product_id)->where('unit_id',$product->productUnitConversion->product_base_unit_id)->sum('quantity');
                    $quantitybaseunitproducts = $searchbaseunitproducts * $product->productUnitConversion->conversion_rate;
                    $searchsecondaryunitproducts = Productsale::where('product_id',$productSale->product_id)->where('unit_id',$product->productUnitConversion->product_secondary_unit_id)->sum('quantity');
                    $totalsalesquantity = $quantitybaseunitproducts + $searchsecondaryunitproducts;
            
                    $buyingstock = $totalsalesquantity/$product->productUnitConversion->conversion_rate; 
                    if($buyingstock<1){
                       continue;
                    }
                    else{
                        $productstock = ceil($product->stock->previous_stock - $buyingstock);
                        $productStock->product_stock = $productstock;
                        $productStock->save();
                    }
                   
                }
            }
        }
    }
    if (!$sale) {
        return response()->json(["message" => "Sale record not found."], 404);
    }

    $sale->status = "sale";
    $sale->save();
    return response()->json([
        'message' => 'Quotation converted to sale successfully'
    ], 200);
}






public function updateSaleQuotation(Request $request){
    $validator = Validator::make($request->all(), [
        "sale_id" => "required|exists:sales,id",
        "party_id" => "nullable|integer",
        "billing_name" => "nullable|string",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        "sales_document" => "nullable",
        "received_amount" => "nullable|integer",
        "payment_type_id" => "nullable|integer",
        "invoice_no" => 'required|integer',
        "invoice_date" => "nullable|string",
        "due_date" => "nullable|string",
        "reference_number" => "nullable|integer",
        "billing_address" => "nullable|string",
        
        // Item validation
        "items" => "nullable|array",
        "items.*.id" => "nullable|integer|exists:productsales,id",
        "items.*.product_id" => "nullable|integer|exists:products,id",
        "items.*.quantity" => "nullable|integer|min:1",
        "items.*.price_per_unit" => "nullable|numeric|min:0",
        "items.*.discount_percentage" => "nullable|integer|min:0",
        "items.*.discount_amount" => "nullable|numeric|min:0",
        "items.*.tax_percentage" => "nullable|integer|min:0",
        "items.*.tax_amount" => "nullable|numeric|min:0",
        "items.*.unit_id" => "nullable|integer",
        "items.*.product_amount" => "nullable|numeric|min:0",
        "total_amount" => "nullable|numeric|min:0"
    ]);

    if ($validator->fails()) {
        return response()->json(["errors" => $validator->errors()], 422);
    }
    dd("ok");
    $sale = Sale::with('productSales')->find($request->sale_id);
    if (!$sale) {
        return response()->json(["message" => "Sale record not found."], 404);
    }

    $sale->update($request->only([
        "party_id", "billing_name", "billing_address", "phone_number",  
        "po_number", "po_date", "sale_description", "received_amount",
        "payment_type_id", "invoice_no", "invoice_date", "due_date",
        "reference_number", "total_amount"
    ]));   
    
    if ($request->hasFile('sale_image')) {
        $imagePath = $request->file('sale_image')->store('sale_images', 'public');
        $sale->update(['sale_image' => $imagePath]);
    }

    if ($request->hasFile('sales_document')) {
        $documentPath = $request->file('sales_document')->store('sales_documents', 'public');
        $sale->update(['sales_document' => $documentPath]);
    }

    $existingProductSales = $sale->productSales->keyBy('id');

    if ($request->has('items')) {
        foreach ($request->items as $item) {
            if (!empty($item['id']) && $existingProductSales->has($item['id'])) {
                $existingProduct = $existingProductSales[$item['id']];
                $existingProduct->update([
                    "product_id" => $item['product_id'],
                    "quantity" => $item['quantity'],
                    "price_per_unit" => $item['price_per_unit'],
                    "discount_percentage" => $item['discount_percentage'] ?? 0,
                    "discount_amount" => $item['discount_amount'] ?? 0,
                    "tax_percentage" => $item['tax_percentage'] ?? 0,
                    "tax_amount" => $item['tax_amount'] ?? 0,
                    "unit_id" => $item['unit_id'],
                    "product_amount" => $item['product_amount']
                ]);
            } else {
                $sale->productSales()->create([
                    "product_id" => $item['product_id'],
                    "quantity" => $item['quantity'],
                    "price_per_unit" => $item['price_per_unit'],
                    "discount_percentage" => $item['discount_percentage'] ?? 0,
                    "discount_amount" => $item['discount_amount'] ?? 0,
                    "tax_percentage" => $item['tax_percentage'] ?? 0,
                    "tax_amount" => $item['tax_amount'] ?? 0,
                    "unit_id" => $item['unit_id'],
                    "product_amount" => $item['product_amount']
                ]);
            }
        }
    }

    return response()->json([
        "message" => "Sale quotation updated successfully."
    ]);
}




public function deleteSaleQuotation($sale_id){

    $sale = Sale::with('productSales')->find($sale_id);
    if (!$sale) {
        return response()->json(["message" => "Sale record not found."], 404);
    }
    $sale->delete();
    return response()->json([
        'message' => 'Sale quotation deleted successfully.'
    ], 200);
}




public function addPaymentIn(Request $request)
{
    $user = auth()->user();
    $searchMainTenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
    $searchTenantUnit = TenantUnit::where('tenant_id', $searchMainTenant->id)->where('isactive', 1)->first();

    $validator = Validator::make($request->all(), [
        "party_id" => "required|integer",
        "payment_type_id" => "required|integer",
        "add_description" => "nullable|string",
        "paymentin_image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        "received_amount" => "required|integer",
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    $paymentin = new Paymentin();
    $paymentin->party_id = $request->party_id;
    $paymentin->payment_type_id = $request->payment_type_id;
    $paymentin->add_description = $request->add_description;
    $paymentin->received_amount = $request->received_amount;
    $paymentin->tenant_unit_id = $searchTenantUnit->id;

    // Handle image upload
    if ($request->hasFile('paymentin_image')) {
        $image = $request->file('paymentin_image');
        $imagePath = $image->store('paymentin_images', 'public');
        $paymentin->paymentin_image = $imagePath;
    }
    $paymentin->save();

    $searchforparty = Party::find($request->party_id);
    
    
    if($searchforparty->topayortorecive == 1 && $searchforparty->opening_balance >= $request->received_amount){
        $searchforparty->opening_balance = $searchforparty->opening_balance - $request->received_amount;
        $searchforparty->save();
        return response()->json([
            'message' => 'Payment added successfully',
            'data' => $paymentin,
        ], 200);
    }else{
        return response()->json([
            'message' => 'Payment failed',
        ], 400);
    }
}








public function getPaymentInData(Request $request){
    $user = auth()->user();
    $validator = Validator::make($request->all(), [
        'salefilter' => "nullable",
        "startdate" => "required_if:salefilter",
        "enddate" => "required_if:salefilter|after_or_equal:startdate",
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    try {
        switch ($request->salefilter) {
            case "This month":
                $startdate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                break;

            case "Last month":
                $startdate = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;

            case "Last quarter":
                $currentMonth = \Carbon\Carbon::now()->month;
                $currentQuarter = ceil($currentMonth / 3);
                $lastQuarter = $currentQuarter - 1;
                $year = \Carbon\Carbon::now()->year;

                if ($lastQuarter == 0) {
                    $lastQuarter = 4;
                    $year--;
                }

                $startdate = \Carbon\Carbon::createFromDate($year, ($lastQuarter - 1) * 3 + 1, 1)->startOfMonth()->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromDate($year, $lastQuarter * 3, 1)->endOfMonth()->format('Y-m-d');
                break;

            case "This year":
                $startdate = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
                $enddate = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
                break;

            case "Custom":
                $startdate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->startdate)->format('Y-m-d');
                $enddate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->enddate)->format('Y-m-d');
                break;

            default:
                return response()->json([
                    'message' => 'Invalid filter provided.',
                ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Date conversion failed',
            'error' => $e->getMessage(),
        ], 400);
    }

    // Retrieve active tenant for the authenticated user
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    if (!$maintenant) {
        return response()->json([
            'message' => 'No active tenant found for this user.',
        ], 404);
    }

    // Get the first active TenantUnit
    $tenantUnit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city'])
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();

    if (!$tenantUnit) {
        return response()->json([
            'message' => 'No active tenant unit found.',
        ], 404);
    }

  $paymentindata = Paymentin::with('party')->where('tenant_unit_id', $tenantUnit->id)
        ->whereBetween('created_at', [$startdate, $enddate])
        ->get();


        return response()->json([
            'message' => $paymentindata,
        ], 400);

}







public function addSaleOrder(Request $request){
    $validator = Validator::make($request->all(), [
        "party_id" => "nullable|integer",
        "billing_name" => "nullable|string",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        "sales_document" => "nullable",
        "payment_type_id" => "required|integer",
        "invoice_no" => 'required|integer',
        "invoice_date" => "nullable|string",
        "due_date" => "nullable|string",
        "reference_number" => "nullable|integer",
        "billing_address" => "nullable|string",
        // Items array validation
        "items" => "required|array",
        "items.*.product_id" => "required|integer|exists:products,id",
        "items.*.quantity" => "required|integer",
        "items.*.price_per_unit" => "required", 
        "items.*.discount_percentage" => "nullable|integer", 
        "items.*.discount_amount" => "nullable|integer",
        "items.*.tax_percentage" => "nullable|integer",
        "items.*.tax_amount" => "nullable|integer",
        "items.*.unit_id" => "required|integer",
        "items.*.product_amount" => "required",
        "total_amount" => "required",
    ]);   
    
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    $user = auth()->user(); 
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();
    $tenants = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();
      
    $sale = new Sale();
    $sale->sale_type = 0;
    $sale->party_id = $request->party_id;
    $sale->billing_name = $request->billing_name;
    $sale->phone_number = $request->phone_number;
    $sale->po_number = $request->po_number;
    $sale->po_date = $request->po_date;
    $sale->received_amount = $request->received_amount;
    $sale->payment_type_id = $request->payment_type_id;
    $sale->sale_description = $request->sale_description;
    $sale->status = "Order Overdue";
    $sale->invoice_no = $request->invoice_no;
    $sale->invoice_date = $request->invoice_date;
    $sale->due_date = $request->due_date;
    $sale->reference_no = $request->reference_no;
    $sale->total_amount = $request->total_amount;
    $sale->tenant_unit_id = $tenants->id;
    $sale->billing_address = $request->billing_address;
    if($request->sale_type == 1){
        $sale->sales_status = "Unpaid";
    }else{
        $sale->sales_status = "Paid";
    }
    if ($request->hasFile('sale_image')) {
        $image = $request->file('sale_image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('sales_images', $imageName, 'public'); 
        $sale->sale_image = $imagePath;
    }
    if ($request->hasFile('sales_document')) {
        $document = $request->file('sales_document');
        $documentName = time() . '_' . $document->getClientOriginalName();
        $documentPath = $document->storeAs('sales_documents', $documentName, 'public'); 
        $sale->sales_document = $documentPath;
    }
    
    $sale->save();
    $products = []; // Array to store product details

    foreach ($request->items as $item) {
        $productSale = new ProductSale();
        $productSale->product_id = $item['product_id'];
        $productSale->quantity = $item['quantity'];
        $productSale->amount = $item['product_amount'];
        $productSale->unit_id = $item['unit_id'];
        $productSale->priceperunit = $item['price_per_unit'];
        $productSale->discount_percentage = $item['discount_percentage'] ?? null;
        $productSale->discount_amount = $item['discount_amount'] ?? null;
        $productSale->tax_percentage = $item['tax_percentage'] ?? null;
        $productSale->tax_amount = $item['tax_amount'] ?? null;
        $productSale->sale_id = $sale->id;
        $productSale->save();
       
        $product = Product::find($item['product_id']);
        if ($product) {
            $products[] = $product;
        }
    }

    return response()->json([
        "invoice_number" => $sale->invoice_no,
        "date" => $sale->invoice_date,
        "due_date" => $sale->due_date,
        "reference_no" => $sale->reference_no,
        "billing_name" => $sale->billing_name,
        "phone_number" => $sale->phone_number,
        "address" => $sale->billing_address ?? "N/A",
        "items" => collect($request->items)->map(function ($item, $index) {
            $product = Product::find($item['product_id']);
            $taxes = $product ? ProductTaxRate::where('id', $product->tax_id)->get() : collect([]);
            
            return [
                "no" => $index + 1,
                "product_name" => $product ? $product->product_name : "N/A",
                "description" => $product ? $product->description : "N/A",
                "quantity" => $item['quantity'],
                "unit_price" => $item['price_per_unit'],
                "total" => $item['product_amount'],
                "tax" => $taxes->map(function ($tax) {
                    return [
                        "tax_name" => $tax->product_tax_name,
                        "tax_percentage" => $tax->product_tax_rate
                    ];
                })
            ];
        }),
        "total_amount" => $sale->total_amount
    ], 200);
     
}




public function deliveryChallan(Request $request){
    $validator = Validator::make($request->all(), [
        "party_id" => "required|integer",
        "phone_number" => "nullable|string",
        "po_number" => "required|numeric",
        "po_date" => "required|string",
        "sale_description" => "nullable|string",
        "sale_image" => "nullable",
        "received_amount" => "required|integer",
        "payment_type_id" => "required|integer",
        "items" => "required|array",
        "items.*.product_id" => "required|integer",
        "items.*.quantity" => "required|integer",
        "items.*.price_per_unit" => "required|integer",
        "items.*.item_amount" => "required|integer", 
        "items.*.discount_percentage" => "nullable|integer", 
        "items.*.tax_amount" => "nullable|integer",
    ]);
   
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 400);
    }

    $user = auth()->user();  
    $sale = new Sale();
    $sale->sale_type = 1;
    $sale->party_id = $request->party_id;
    $sale->phone_number = $request->phone_number;
    $sale->po_number = $request->po_number;
    $sale->po_date = $request->po_date;
    $sale->received_amount = $request->received_amount;
    $sale->payment_type_id = $request->payment_type_id;
    $sale->sale_description = $request->sale_description;
    $sale->sale_image = $request->sale_image;
    $sale->user_id = $user->id;
    $sale->status = "Order overdue";
    $sale->save();

    // Iterate over the items and store them in the ProductSale table
    foreach ($request->items as $item) {
        $productSale = new ProductSale();
        $productSale->product_id = $item['product_id'];
        $productSale->quantity = $item['quantity'];
        $productSale->amount = $item['item_amount'];
        $productSale->unit_id= $item['unit_id'];
        $productSale->priceperunit = $item['price_per_unit'];
        $productSale->discount_percentage = $item['discount_percentage'] ?? null;
        $productSale->discount_amount = $item['discount_amount'] ?? null;
        $productSale->tax_percentage = $item['tax_percentage']?? null;
        $productSale->tax_amount = $item['tax_amount'] ?? null;
        $productSale->sale_id = $sale->id;
        $productSale->save();
    }

    return response()->json([
        'message' => 'Quotation created successfully',
        'data' => $request->all(),
    ], 200);
}

}

