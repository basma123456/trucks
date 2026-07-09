<?php


namespace App\Services;


use App\Models\Item;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ItemsService
{
    public function items($request, $query, $query2)
    {
        if ($request->price_from) {
            $query->where('price', '>=', $request->price_from);

            $query2->whereRaw(
                '(COALESCE(pricing.total_price,0) + COALESCE(pricing.purchase_cost,0)) >= ?',
                [$request->price_from]
            );
        }

        if ($request->price_to) {
            $query->where('price', '<=', $request->price_to);

            $query2->whereRaw(
                '(COALESCE(pricing.total_price,0) + COALESCE(pricing.purchase_cost,0)) <= ?',
                [$request->price_to]
            );
        }


        if ($request->search) {
            $query->where("product_name", "like", "%" . $request->search . "%");

            $query2->where("product_name", "like", "%" . $request->search . "%");
        }


        $dbItems = $query->select('id', "code", 'product_name', 'thumbnail',
            'part_description',
            'category', 'subcategory',
            'type',
            'price', 'brand_id', 'brand_code')->get()->toArray();


        $data = $query2->select(
            'items.*',
            'brands.name as brand_name',
            DB::raw('COALESCE(pricing.total_price,0) as total_price'),
            DB::raw('COALESCE(pricing.purchase_cost,0) as purchase_cost')
        )->get();

        $apIdataArray = $data->toArray();
        $all = array_merge($apIdataArray, $dbItems);


        return ['all' => $all, 'apIdataArray' => $apIdataArray, 'dbItems' => $dbItems];

    }


    public function paginationOfItems($data, $perPage)
    {
        /***********customized pagination for items page only *************/
        $perPage = $perPage ?? 20;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginated = new LengthAwarePaginator(
            $data->forPage($page, $perPage)->values(),
            $data->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        return $paginated;
        /***********end customized pagination for items page only *************/

    }


    public function localItemsQuery($brandId)
    {
        /*************************local items query*************/
        $query = Item::where('type', 'local')->whereHas('brand', function ($q) use ($brandId) {
            $q->where(['status' => 1])->whereIn('code', $brandId);
        });
        return $query;
        /*****************end  local items query************/
    }


    public function pricingQuery()
    {
        return DB::table('pricing_lists')
            ->select(
                'item_code',
                DB::raw('SUM(price) as total_price'),
                DB::raw('MIN(purchase_cost) as purchase_cost')
            )
            ->groupBy('item_code');
    }




    public function apiItemsQuery($pricingQuery , $brandId){
        return Item::query()
            ->join('brands', 'items.brand_code', '=', 'brands.code')
            ->joinSub($pricingQuery, 'pricing', function ($join) {
                $join->on('items.code', '=', 'pricing.item_code');
            })
            ->where('brands.status', 1)
            ->where('brands.type', 'api')
            ->whereIn('items.brand_code', $brandId);

    }
}











