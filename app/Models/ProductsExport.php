<?php

namespace App\Models;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\Product;
use App\Models\ProductStock;
use App\Utility\ProductUtility;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Product::all();
    }
     /**
    * @var Product $product
    */
    public function headings(): array
    {
        $output = [
            'name',
            'description',
            'category_id',
            'brand_id',
            'unit_price',
            'unit',
            'stock',
            'weight',
            'est_shipping_days',
            'meta_title',
            'meta_description',
            'variant',
        ];
        return $output;
    }

    /**
    * @var Product $product
    */
    public function map($product): array
    {
        $qty = 0;
        $options = json_decode($product->choice_options, true);
        $combinations = (new CombinationService())->generate_combination($options);
        // $count = count($combinations);
        // foreach ($product->stocks as $key => $stock) {
        foreach ($combinations as $key => $combination) {
            $str = ProductUtility::get_combination_string($combination, $options);

            $stock = ProductStock::where('variant', $str)->get();

            $qty = $stock->qty;

            $category = Category::where('id', $product->category_id)->get();
            $brand = Brand::where('id', $product->brand_id)->get();

            $variation = [
                $product->name,
                // $product->description,
                $count,
                $category[0]->name,
                $brand[0]->name,
                $stock->price,
                $product->unit,
                $qty,
                $stock->weight,
                $product->est_shipping_days,
                $product->meta_title,
                $product->meta_description,
                $stock->variant,
            ];
            $output[] = $variation;
        }
        return $output;
    }
}
