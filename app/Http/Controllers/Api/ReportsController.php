<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Sale;
use App\Models\TenantUnit;
use App\Models\Purchase;
class ReportsController extends Controller
{


  public function dayBookReport()
{
    $user = auth()->user();
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    $tenantunit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();

    $sales = Sale::where('tenant_unit_id', $tenantunit->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    $purchases = Purchase::where('tenant_unit_id', $tenantunit->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    $mergedData = $sales->merge($purchases);

    return response()->json([
        'data' => $mergedData
    ]);
}



public function allTransaction(Request $request){
    $user = auth()->user();
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    $tenantunit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();
   
    $sales = Sale::where('tenant_unit_id', $tenantunit->id)
        ->get();
    $purchases = Purchase::where('tenant_unit_id', $tenantunit->id)
        ->get();
    $mergedData = $sales->merge($purchases);
    return response()->json([
        'data' => $mergedData
    ]);
}


  public function profitLossReport(Request $request){
    $user = auth()->user();
    $maintenant = Tenant::where('user_id', $user->id)->where('isactive', 1)->first();

    $tenantunit = TenantUnit::with(['user', 'businesstype', 'businesscategory', 'state', 'city']) 
        ->where('tenant_id', $maintenant->id)
        ->where('isactive', 1)
        ->first();

    $sales = Sale::where('tenant_unit_id', $tenantunit->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    $purchases = Purchase::where('tenant_unit_id', $tenantunit->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    $totalSales = $sales->sum('total_amount');
    $totalPurchases = $purchases->sum('total_amount');
    $profit = $totalSales - $totalPurchases;

    return response()->json([
        'total_sales' => $totalSales,
        'total_purchases' => $totalPurchases,
        'profit' => $profit
    ]);}
}