<?php

namespace Lunar\Hub\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Lunar\Hub\LunarHub;
use Lunar\Models\Product;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductsController extends Controller
{
    public function clone(Product $product)
    {
	    $cloneProduct = $product->replicate();
	    $cloneProduct->push();

		## name
	    $attributeData = $cloneProduct->attribute_data->toArray();
	    $name = $attributeData['name']->getValue();

	    foreach($name as $k => $v)
		    $name[$k]->setValue($name[$k]->getValue().' Copy');

	    $attributeData['name']->setValue($name);

		$cloneProduct->attribute_data = $attributeData;
	    $cloneProduct->save();

	    ## variants with options
	    foreach ($product->variants as $variant)
	    {
		    $cloned = $variant->replicate();
		    $cloneProduct->variants()->save($cloned);

			foreach($variant->values as $value)
			{
				$cloned2 = $value->replicate();
				$cloned->values()->save($cloned2);
			}
	    }

	    ## collections
	    foreach ($product->collections as $collection)
	    {
		    $cloned = $collection->replicate();
		    $cloneProduct->collections()->save($cloned);
	    }

	    ## associations
	    foreach ($product->associations as $association)
	    {
		    $cloned = $association->replicate();
		    $cloneProduct->associations()->save($cloned);
	    }

	    return response()->redirectToRoute('hub.products.show', $cloneProduct);
    }
}
