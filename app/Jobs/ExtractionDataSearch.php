<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\ExtractionHeaderModel;
use App\Models\Admin\ExtractionModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ExtractionDataSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $extractionHeader;
    protected $urlBase;
    protected $caracter_1;
    protected $caracter_2;
    protected $caracter_3;
    protected $caracter_4;
    protected $caracter_5;
    protected $not_result;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExtractionHeaderModel $extractionHeader)
    {
        $this->extractionHeader = $extractionHeader;
        $this->urlBase = 'http://192.168.1.190:81/api/';
        $this->caracter_1 = ' ';
        $this->caracter_2 = ',';
        $this->caracter_3 = '-';
        $this->caracter_4 = ')';
        $this->caracter_5 = ']';
        $this->not_result = '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->extractionHeader->id > 0) {
            //$this->processMissingBrand();
            //$this->processMissingArticleBrand();
            //$this->processMissingProvider();
            $this->processMissingCustomer();//importadores
            //$this->processMissingCountry();
        }

        //$lstProviders = DB::table("extraction_subida")->selectRaw('DISTINCT(marca) as codMarca')->where('extractionHeaderId', $this->extractionHeader->id)->get();
    }

    function processMissingBrand(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizar_marcas_faltantes  :::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        //$response = Http::acceptJson()->get('http://192.168.1.190:81/api/ecommerce/getTrademarks');
        //$lstBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getTrademarks'));
        //var_dump($response->getBody()->getContents());
        //obtenemos la lista de los productos y marca
        $lstBrand = DB::table('mst_product_brand')->orderBy('lengthName','DESC')->get();
        if (count($lstBrand) > 0) {
            //:::: ACTUALIZAMOS LA CABECERA ::::
            $totalRegistros = DB::table("extraction_subida")->where('extractionHeaderId', $this->extractionHeader->id)->count();
            DB::table('extraction_header')->where('id', $this->extractionHeader->id)->update(array('status' => 'RUNNING','totalRegister' => $totalRegistros));
            // :: procedemos una busqueda por la marca completa
            foreach ($lstBrand as $d => $brand) {
                print_r("fila marca : ".$d."\n" );
                $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$brand->name,false);
                //PROCEDEMOS A BUSCAR DE MANERA PARCIAL POR EL CARACTER 1
                $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_1));
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true);
                if (!$result) {
                    $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_1, strlen(trim($brand->name))));
                    $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true);
                }
                //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 2
                $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_2));
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true);
                if (!$result) {
                    $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_2, strlen(trim($brand->name))));
                    $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true);
                }
                //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 3
                $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_3));
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true);
                if (!$result) {
                    $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_3, strlen(trim($brand->name))));
                    $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true);
                }
                //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 4
                $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_4));
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true);
                if (!$result) {
                    $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_4, strlen(trim($brand->name))));
                    $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true);
                }
                //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 5
                $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_5));
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true);
                if (!$result) {
                    $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_5, strlen(trim($brand->name))));
                    $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true);
                }
            }
            //actualizamos los registros que no se pudieron encontrar
            print_r(":::::::::::::: ACTUALIZAMOS LOS REGISTROS NO ENCONTRADOS::::::::::::\n" );
            DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                        ->where('status', 'CHARGED')
                                        ->update(array('status' => 'NOT_FOUND', 'typeFoundColor' => 'badge bg-danger'));
            // actualizamos la cabecera
            $totalFounded = DB::table("extraction_subida")->where('extractionHeaderId', $this->extractionHeader->id)->where('status','FOUNDED')->count();
            $totalPartialFound = DB::table("extraction_subida")->where('extractionHeaderId', $this->extractionHeader->id)->where('status','PARTIAL_FOUND')->count();
            $totalNotFound = DB::table("extraction_subida")->where('extractionHeaderId', $this->extractionHeader->id)->where('status','NOT_FOUND')->count();
            DB::table('extraction_header')->where('id', $this->extractionHeader->id)->update(array('status' => 'COMPLETE','totalFound' => $totalFounded,'totalPartialFound' => $totalPartialFound,'totalMissing' => $totalNotFound));
        }
    }

    function processMissingArticleBrand(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::::::: regularizamos_articulos_por_marca ::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstBrandFounded = DB::table("extraction_subida")->selectRaw('DISTINCT(marca) as codMarca')->where('extractionHeaderId', $this->extractionHeader->id)->get();
        if (count($lstBrandFounded)>0) {
            foreach ($lstBrandFounded as $b => $brand) {
                print_r("orden: ".$b." - Marca: ".$brand->codMarca."\n" );
                //if ($brand->codMarca != '001') {
                $this->searchArticle($brand->codMarca);
                //}
            }
        }
    }

    function processMissingCustomer(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::::::::: regularizamos_importadores :::::::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstCustomer = DB::table("extraction_subida")->selectRaw('DISTINCT(TRIM(importador)) as customer')->where('extractionHeaderId', $this->extractionHeader->id)->get();
        if (count($lstCustomer) > 0) {
            //var_dump($lstCustomer);
            foreach ($lstCustomer as $c => $customer) {
                $response = Http::post($this->urlBase .'customers/getCustomerByName', [
                    'customer_name' => 'EMP.DE TRANSP.NORTE'
                ]);
                $data = $response->body();
                $arrayString = substr($data, 0, -1);
                var_dump($arrayString);
                $dataFinal = $response->body()->toArray();
                var_dump($dataFinal);
                //var_dump($data[0]);
                //var_dump($data[0]['customer_code']);
                die;
            }
        }


    }

    function processMissingProvider(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::: regularizamos_proveedores_importadores :::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstProviders = json_decode(file_get_contents($this->urlBase .'general/getProviders'));
        if (count($lstProviders->lista) > 0) {
            foreach ($lstProviders->lista as $l => $lista) {
                print_r("fila proveedores : ".$l."\n" );
                if ($lista->idproveedor != "010328") { // arreglar razon social
                    //$this->searchImporter($lista->idproveedor,trim($lista->razonsocial),trim($lista->razonsocial),false); CLIENTES
                    $this->searchProvider($lista->idproveedor,trim($lista->razonsocial),trim($lista->razonsocial),false);
                }
            }
        }
    }

    function processMissingCountry(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizamos_paises_faltantes :::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstCountry = json_decode(file_get_contents($this->urlBase .'general/getCountries'));
        if (count($lstCountry) > 0) {
            foreach ($lstCountry as $c => $country) {
                $this->searchCountryByName($this->extractionHeader->id,$country->codigo,$country->pais);
            }
        }
    }

    function searchBrandByName($extractionHeader,$brand_code,$brand_name_ini,$brand_to_search,$is_partial=false){
        if (strlen(trim($brand_to_search))> 0) {
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    "'.$brand_to_search.'" AS BRAND_TO_SEARCH,
                                    LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)) as POSICION,
                                    (LOCATE("'.$brand_to_search.'", UPPER(descripcionComercial)) + LENGTH("'.$brand_to_search.'") - 1) as FINAL_WORD,
                                    LENGTH(descripcionComercial) as total_cadena,
                                    IF((LOCATE("'.$this->caracter_1.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_1.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_ESPACIO,
                                    IF((LOCATE("'.$this->caracter_2.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_2.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_COMA,
                                    IF((LOCATE("'.$this->caracter_3.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_3.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_GUION,
                                    IF((LOCATE("'.$this->caracter_4.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_4.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_PARENTESIS,
                                    IF((LOCATE("'.$this->caracter_5.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_5.'",UPPER(descripcionComercial), LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$brand_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_CORCHETES
                                    ')
                                    ->where('extractionHeaderId', $extractionHeader)
                                    ->whereIn('status',['CHARGED','PARTIAL_FOUND'])
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($brand_to_search)).'%"')
                                    ->get();
            if(count($listFounded)>0){
                $this->validateBrandFounded($listFounded,$brand_code,$brand_name_ini,$brand_to_search,$is_partial);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function validateBrandFounded($listFounded,$brand_code,$brand_name_ini,$brand_to_search,$is_partial){
        $status_founded=false;
        foreach ($listFounded as $lst => $found) {
            //print_r("caracter anterior: ".$found->CARACTER_ANTERIOR." - validacion:  ".ctype_alpha($found->CARACTER_ANTERIOR)."\n" );
            if(!ctype_alpha($found->CARACTER_ANTERIOR)){
                switch($brand_to_search){
                    case (strtoupper(trim($found->FINAL_ESPACIO)) == $brand_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_COMA)) == $brand_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_GUION)) == $brand_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_PARENTESIS)) == $brand_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_CORCHETES)) == $brand_to_search):
                        $status_founded=true;
                        break;
                    default:
                        if ($found->FINAL_WORD == $found->total_cadena) {
                            $status_founded = true;
                        }else{
                            $status_founded = false;
                        }
                        break;
                }

                if ($status_founded) {
                    if ($is_partial) {
                        //print_r("partials brand: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand_code, 'nameMarca' => $brand_name_ini, 'typeFoundColor' => 'badge bg-warning'));
                    }else{
                        //print_r("FOUNDED: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('status' => 'FOUNDED', 'marca' => $brand_code, 'nameMarca' => $brand_name_ini, 'typeFoundColor' => 'badge bg-success'));
                    }
                }
            }
        }
    }

    function searchArticle($codeBrand){
        if (strlen(trim($codeBrand))>0) {
            $lstArticleBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getProductsByTrademark/'.$codeBrand));
            if (count($lstArticleBrand->data)>0) {
                foreach ($lstArticleBrand->data as $d => $brand) {
                    //buscamos el articulo tal cual
                    $this->searchArticleByCodeBrand($codeBrand,trim($brand->factory_code),trim($brand->factory_code),false);
                    //PROCEDEMOS A BUSCAR DE MANERA PARCIAL POR EL CARACTER 1
                    $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_1));
                    if (strlen(trim($txtPartialIni))>3) {
                        $result = $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialIni,true);
                        if (!$result) {
                            $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_1, strlen(trim($brand->factory_code))));
                            if (strlen(trim($txtPartialFinal))>3) {
                                $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialFinal,true);
                            }
                        }
                    }

                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 2
                    $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_2));
                    if (strlen(trim($txtPartialIni))>3) {
                        $result = $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialIni,true);
                        if (!$result) {
                            $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_2, strlen(trim($brand->factory_code))));
                            if (strlen(trim($txtPartialFinal))>3) {
                                $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialFinal,true);
                            }
                        }
                    }

                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 3
                    $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_3));
                    if (strlen(trim($txtPartialIni))>3) {
                        $result = $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialIni,true);
                        if (!$result) {
                            $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_3, strlen(trim($brand->factory_code))));
                            if (strlen(trim($txtPartialFinal))>3) {
                                $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialFinal,true);
                            }
                        }
                    }


                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 4
                    $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_4));
                    if (strlen(trim($txtPartialIni))>3) {
                        $result = $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialIni,true);
                        if (!$result) {
                            $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_4, strlen(trim($brand->factory_code))));
                            if (strlen(trim($txtPartialFinal))>3) {
                                $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialFinal,true);
                            }
                        }
                    }


                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 5
                    $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_5));
                    if (strlen(trim($txtPartialIni))>3) {
                        $result = $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialIni,true);
                        if (!$result) {
                            $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_5, strlen(trim($brand->factory_code))));
                            if (strlen(trim($txtPartialFinal))>3) {
                                $this->searchArticleByCodeBrand($codeBrand,$brand->factory_code,$txtPartialFinal,true);
                            }
                        }
                    }
                }
            }
        }
    }

    function searchArticleByCodeBrand($brand_code,$article_ini,$article_to_search,$is_partial){
        $article_to_search = str_replace('"', '', $article_to_search);
        if (strlen(trim($article_to_search))> 3) {
            print_r("busqueda Articulo: ".$article_to_search."\n" );
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    "'.$article_to_search.'" AS article_to_search,
                                    LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)) as POSICION,
                                    (LOCATE("'.$article_to_search.'", UPPER(descripcionComercial)) + LENGTH("'.$article_to_search.'") - 1) as FINAL_WORD,
                                    LENGTH(descripcionComercial) as total_cadena,
                                    IF((LOCATE("'.$this->caracter_1.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_1.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_ESPACIO,
                                    IF((LOCATE("'.$this->caracter_2.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_2.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_COMA,
                                    IF((LOCATE("'.$this->caracter_3.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_3.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_GUION,
                                    IF((LOCATE("'.$this->caracter_4.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_4.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_PARENTESIS,
                                    IF((LOCATE("'.$this->caracter_5.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)),((LOCATE("'.$this->caracter_5.'",UPPER(descripcionComercial), LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))) - LOCATE("'.$article_to_search.'",UPPER(descripcionComercial)))),
                                    "'.$this->not_result.'") AS FINAL_CORCHETES
                                    ')
                                    ->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('marca',$brand_code)
                                    ->whereIn('statusArticle',['CHARGED','PARTIAL_FOUND'])
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($article_to_search)).'%"')
                                    ->get();
            if(count($listFounded)>0){
                print_r("brand: ".$brand_code." ArticuloInicial: ".$article_ini." articuloSearch: ".$article_to_search."\n" );
                $this->validateArticleFounded($listFounded,$article_ini,$article_to_search,$is_partial);
                return true;
            }
            return false;
        }else{
            return false;
        }
    }

    function validateArticleFounded($listFounded,$article_ini,$article_to_search,$is_partial){
        $status_founded=false;
        foreach ($listFounded as $lst => $found) {
            //print_r("caracter anterior: ".$found->CARACTER_ANTERIOR." - validacion:  ".ctype_alpha($found->CARACTER_ANTERIOR)."\n" );
            if(!ctype_alpha($found->CARACTER_ANTERIOR)){
                switch($article_to_search){
                    case (strtoupper(trim($found->FINAL_ESPACIO)) == $article_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_COMA)) == $article_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_GUION)) == $article_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_PARENTESIS)) == $article_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_CORCHETES)) == $article_to_search):
                        $status_founded=true;
                        break;
                    default:
                        if ($found->FINAL_WORD == $found->total_cadena) {
                            $status_founded = true;
                        }else{
                            $status_founded = false;
                        }
                        break;
                }

                if ($status_founded) {
                    if ($is_partial) {
                        //print_r("partials brand: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'PARTIAL_FOUND','codigo' => $article_ini,'typeFoundArticle' => 'badge bg-warning'));
                    }else{
                        //print_r("FOUNDED: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'FOUNDED','codigo' => $article_ini,'typeFoundArticle' => 'badge bg-success'));
                    }
                }
            }
        }
    }

    function searchImporter($codigo,$importer_ini,$importer_to_search,$is_partial){
        if (strlen(trim($importer_to_search))> 0) {
            print_r("importerSearchSQL: ".$importer_to_search."\n" );
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(importador),
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",importador)-1,1) AS CARACTER_ANTERIOR,
                                    "'.$importer_to_search.'" AS importer_to_search,
                                    LOCATE("'.$importer_to_search.'",UPPER(importador)) as POSICION,
                                    (LOCATE("'.$importer_to_search.'", UPPER(importador)) + LENGTH("'.$importer_to_search.'") - 1) as FINAL_WORD,
                                    LENGTH(importador) as total_cadena,
                                    IF((LOCATE("'.$this->caracter_1.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",UPPER(importador)),((LOCATE("'.$this->caracter_1.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador)))) - LOCATE("'.$importer_to_search.'",UPPER(importador)))),
                                    "'.$this->not_result.'") AS FINAL_ESPACIO,
                                    IF((LOCATE("'.$this->caracter_2.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",UPPER(importador)),((LOCATE("'.$this->caracter_2.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador)))) - LOCATE("'.$importer_to_search.'",UPPER(importador)))),
                                    "'.$this->not_result.'") AS FINAL_COMA,
                                    IF((LOCATE("'.$this->caracter_3.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",UPPER(importador)),((LOCATE("'.$this->caracter_3.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador)))) - LOCATE("'.$importer_to_search.'",UPPER(importador)))),
                                    "'.$this->not_result.'") AS FINAL_GUION,
                                    IF((LOCATE("'.$this->caracter_4.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",UPPER(importador)),((LOCATE("'.$this->caracter_4.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador)))) - LOCATE("'.$importer_to_search.'",UPPER(importador)))),
                                    "'.$this->not_result.'") AS FINAL_PARENTESIS,
                                    IF((LOCATE("'.$this->caracter_5.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(importador),LOCATE("'.$importer_to_search.'",UPPER(importador)),((LOCATE("'.$this->caracter_5.'",UPPER(importador), LOCATE("'.$importer_to_search.'",UPPER(importador)))) - LOCATE("'.$importer_to_search.'",UPPER(importador)))),
                                    "'.$this->not_result.'") AS FINAL_CORCHETES
                                    ')
                                    ->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->whereIn('statusImporter',['CHARGED','PARTIAL_FOUND'])
                                    ->whereRaw('UPPER(TRIM(importador)) like "%'.strtoupper(trim($importer_to_search)).'%"')
                                    ->get();
            if(count($listFounded)>0){
                print_r("importerInicial: ".$importer_ini." importerSearch: ".$importer_to_search."\n" );
                $this->validateImporterFounded($codigo,$listFounded,$importer_ini,$importer_to_search,$is_partial);
                return true;
            }
            return false;
        }else{
            return false;
        }
    }

    function validateImporterFounded($codigo,$listFounded,$importer_ini,$importer_to_search,$is_partial){
        $status_founded=false;
        foreach ($listFounded as $lst => $found) {
            //print_r("caracter anterior: ".$found->CARACTER_ANTERIOR." - validacion:  ".ctype_alpha($found->CARACTER_ANTERIOR)."\n" );
            if(!ctype_alpha($found->CARACTER_ANTERIOR)){
                switch($importer_to_search){
                    case (strtoupper(trim($found->FINAL_ESPACIO)) == $importer_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_COMA)) == $importer_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_GUION)) == $importer_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_PARENTESIS)) == $importer_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_CORCHETES)) == $importer_to_search):
                        $status_founded=true;
                        break;
                    default:
                        if ($found->FINAL_WORD == $found->total_cadena) {
                            $status_founded = true;
                        }else{
                            $status_founded = false;
                        }
                        break;
                }

                if ($status_founded) {
                    if ($is_partial) {
                        //print_r("partials brand: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusImporter' => 'PARTIAL_FOUND','codImporter' => $codigo));
                    }else{
                        //print_r("FOUNDED: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusImporter' => 'FOUNDED','codImporter' => $codigo));
                    }
                }
            }
        }
    }

    function searchProvider($codigo,$provider_ini,$provider_to_search,$is_partial){
        if (strlen(trim($provider_to_search))> 0) {
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(embarcadorExportador),
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",embarcadorExportador)-1,1) AS CARACTER_ANTERIOR,
                                    "'.$provider_to_search.'" AS provider_to_search,
                                    LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)) as POSICION,
                                    (LOCATE("'.$provider_to_search.'", UPPER(embarcadorExportador)) + LENGTH("'.$provider_to_search.'") - 1) as FINAL_WORD,
                                    LENGTH(embarcadorExportador) as total_cadena,
                                    IF((LOCATE("'.$this->caracter_1.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)),((LOCATE("'.$this->caracter_1.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))) - LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))),
                                    "'.$this->not_result.'") AS FINAL_ESPACIO,
                                    IF((LOCATE("'.$this->caracter_2.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)),((LOCATE("'.$this->caracter_2.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))) - LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))),
                                    "'.$this->not_result.'") AS FINAL_COMA,
                                    IF((LOCATE("'.$this->caracter_3.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)),((LOCATE("'.$this->caracter_3.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))) - LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))),
                                    "'.$this->not_result.'") AS FINAL_GUION,
                                    IF((LOCATE("'.$this->caracter_4.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)),((LOCATE("'.$this->caracter_4.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))) - LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))),
                                    "'.$this->not_result.'") AS FINAL_PARENTESIS,
                                    IF((LOCATE("'.$this->caracter_5.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador))) - 1) > 0, -- ultima posicion de la palabra de busqueda
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)),((LOCATE("'.$this->caracter_5.'",UPPER(embarcadorExportador), LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))) - LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)))),
                                    "'.$this->not_result.'") AS FINAL_CORCHETES
                                    ')
                                    ->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->whereIn('statusProvider',['CHARGED','PARTIAL_FOUND'])
                                    ->whereRaw('UPPER(TRIM(embarcadorExportador)) like "%'.strtoupper(trim($provider_to_search)).'%"')
                                    ->get();
            if(count($listFounded)>0){
                print_r("providerInicial: ".$provider_ini." providerSearch: ".$provider_to_search."\n" );
                $this->validateProviderFounded($codigo,$listFounded,$provider_ini,$provider_to_search,$is_partial);
                return true;
            }
            return false;
        }else{
            return false;
        }
    }

    function validateProviderFounded($codigo,$listFounded,$provider_ini,$provider_to_search,$is_partial){
        $status_founded=false;
        foreach ($listFounded as $lst => $found) {
            //print_r("caracter anterior: ".$found->CARACTER_ANTERIOR." - validacion:  ".ctype_alpha($found->CARACTER_ANTERIOR)."\n" );
            if(!ctype_alpha($found->CARACTER_ANTERIOR)){
                switch($provider_to_search){
                    case (strtoupper(trim($found->FINAL_ESPACIO)) == $provider_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_COMA)) == $provider_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_GUION)) == $provider_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_PARENTESIS)) == $provider_to_search):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_CORCHETES)) == $provider_to_search):
                        $status_founded=true;
                        break;
                    default:
                        if ($found->FINAL_WORD == $found->total_cadena) {
                            $status_founded = true;
                        }else{
                            $status_founded = false;
                        }
                        break;
                }

                if ($status_founded) {
                    if ($is_partial) {
                        //print_r("partials brand: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusProvider' => 'PARTIAL_FOUND','codProvider' => $codigo));
                    }else{
                        //print_r("FOUNDED: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusProvider' => 'FOUNDED','codProvider' => $codigo));
                    }
                }
            }
        }
    }

    function searchCountryByName($extractionHeader,$codCountry,$countryName){
        if (strlen(trim($countryName))> 0) {
            //obtenemos todos los registros de paisorigen segun el nombre del pais
            $listPaisOrigen = DB::table('extraction_subida')
                            ->select('id','paisOrigen')
                            ->where('extractionHeaderId', $extractionHeader)
                            ->whereRaw('UPPER(TRIM(paisOrigen)) like "%'.strtoupper(trim($countryName)).'%"')
                            ->get();
            if (count($listPaisOrigen)> 0) {
                foreach ($listPaisOrigen as $po => $paisOrigen) {
                    //if (strtoupper(trim($paisOrigen->paisOrigen)) == strtoupper(trim($countryName))) {
                        DB::table('extraction_subida')->where('id', $paisOrigen->id)->update(array('codPaisOrigen' => $codCountry));
                    //}
                }
            }

            //obtenemos todos los registros de paisDestino segun el nombre del pais
            $listPaisCompra = DB::table('extraction_subida')
                            ->select('id','paisCompra')
                            ->where('extractionHeaderId', $extractionHeader)
                            ->whereRaw('UPPER(TRIM(paisCompra)) like "%'.strtoupper(trim($countryName)).'%"')
                            ->get();
            if (count($listPaisCompra)> 0) {
                foreach ($listPaisCompra as $pc => $paisCompra) {
                    //if (strtoupper(trim($paisCompra->paisCompra)) == strtoupper(trim($countryName))) {
                        DB::table('extraction_subida')->where('id', $paisCompra->id)->update(array('codPaisCompra' => $codCountry));
                    //}
                }
            }
        }
    }

    public function validateAlpha($attribute, $value)
    {
        return is_string($value) && preg_match('/^[\pL\pM]+$/u', $value);
    }

    function object_sorter($clave,$orden=null) {
        return function ($a, $b) use ($clave,$orden) {
              $result=  ($orden=="DESC") ? strnatcmp($b->$clave, $a->$clave) :  strnatcmp($a->$clave, $b->$clave);
              return $result;
        };
    }
}
