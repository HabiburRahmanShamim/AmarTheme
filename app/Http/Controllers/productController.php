<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Auth;
use Session;
use ZipArchive;
use DB;
class productController extends Controller
{
    public static function getAll(){
        $products=array();
        $TITLES=array('WORDPRESS','HTML');
        for($i=0;$i<2;$i++) {
            $products[$TITLES[$i]]=array();
            $products[$TITLES[$i]]['products'] = DB::select("SELECT products.product_name AS name,
                                          products.product_id as id,
                                          ratings.rating as rating,
                                          images.link as img 
                                     FROM products,ratings,images 
                                    WHERE products.product_category 
                                    LIKE '$TITLES[$i]' 
                                      AND products.product_id=ratings.product_id
                                      AND products.product_id=images.product_id
                                    ORDER BY rand() 
                                    LIMIT 9");
        }
        $products=json_decode(json_encode($products), true);
        return $products;
    }
    public static function get($id){
        $result = DB::select("SELECT products.product_name AS name,
                                          products.product_id as id,
                                          products.product_description as details,
                                          products.product_price as price,
                                          ratings.rating as rating,
                                          images.link as img 
                                     FROM products,ratings,images 
                                    WHERE 
                                      products.product_id=$id 
                                      AND products.product_id=ratings.product_id
                                      AND products.product_id=images.product_id
                                      LIMIT 1");
        $result=json_decode(json_encode($result), true);
        return $result[0];
    }
    public function insertProduct($name,$category,$description,$price,$dev_id)
    {
        $id =DB::table('products')->insertGetId(
            ['product_name' => $name, 'product_description' => $description,
                'product_category'=>$category,'product_type' => 'Event',
                'product_price' => $price,'developer_id' => $dev_id]);
        return $id;
    }
    public function validateUploadedProduct(Request $request)
    {
        if ($request->hasFile('zip')) {
            // your code here
            $file = $request->file('zip');

            //Display File Name
            echo 'File Name: '.$file->getClientOriginalName();
            echo '<br>';

            //Display File Extension
            echo 'File Extension: '.$file->getClientOriginalExtension();
            echo '<br>';
            echo $request->input('name');


            //Move Uploaded File
            $destinationPath = 'themes';
            //insert product entry into database
            //get product id in $fileid

            $filename=productController::insertProduct($request->input('name'),$request->input('category'),$request->input('description'),$request->input('price'),1);
            $filename_without_zip=$filename;
            $filename="".$filename.".zip";

            $file->move($destinationPath,$filename);

            $zippath="themes/".$filename;

            $zip = new ZipArchive;

            $res = $zip->open($zippath);
            if ($res === TRUE) {
                $zip->extractTo("themes/demo/".$filename_without_zip."/");
                $zip->close();
                return redirect('/dashboard');
            } else {
                return 'doh!';
            }

        }
        return "doh!";


    }
    public function validateDownload($id)
    {
        if (1)//check if file exists
            return response()->file("themes/".$id.'.zip');
        return view('home');
    }
    public function show($id){
        return view('product', ['product' => self::get($id)]);
    }

}