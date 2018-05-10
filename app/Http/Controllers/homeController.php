<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Session;
use Validator;
use Auth;
use DB;


class homeController extends Controller
{
    //
    public function index()
    {
        $result = productController::getAll();
        //dd($result);
        $products = json_decode(json_encode($result), true);
        $categories=$products;
        return view('home',compact('categories'));
        //dd($categories);
        $categories = [];
        foreach (array_chunk($products, 3) as $p){
            $categories[] = ['products' => $p];
        }
        return view('home', ["categories" => $categories]);
    }

    public function dashboard()
    {
        if(Session::has('email'))
        {
            return view('dashboard');

        }
        else
        {
            return redirect('/');
        }


    }
    public function demoView($id)
    {
        $redir=""
        return redirect("/public/themes/demo/".$id."/"); //<- Change to themes/demo/... when deploying to server
    }

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }


    public function searchProduct(Request $request)
    {
        $text = $request->input('text');
        $result = DB::select("SELECT DISTINCT products.product_name AS name,
                                          products.product_id as id,
                                          products.product_description as details,
                                          products.product_price as price,
                                          ratings.rating as rating,
                                          images.link as img 
                                     FROM products,ratings,images 
                                    WHERE 
                                      (products.product_name like '%$text%'
                                      OR 
                                      products.product_description like '%$text%'                                   
                                      )
                                      
                                      AND products.product_id=ratings.product_id
                                      AND products.product_id=images.product_id
                                      LIMIT 9
                              ");
        $results=json_decode(json_encode($result), true);
        //return $result;
        return view('search', ['results' => $results]);
    }
    public function productDetails($id)
    {
        $result = productController::get($id);
        $product = json_decode(json_encode($result), true);
        // dd($product);
        return view('product',compact('product'));
    }

    public function registration()
    {
        return view('registration');
    }
}