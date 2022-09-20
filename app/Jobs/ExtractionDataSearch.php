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
            $this->processMissingBrand();
            $this->processMissingArticleBrand();
            $this->processMissingProvider();
            $this->processMissingCustomer();//importadores
            $this->processMissingCountry();
            DB::table('extraction_header')->where('id', $this->extractionHeader->id)->update(array('status' => 'COMPLETE'));
            // $this->processArticleNotFound();
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
                $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$brand->name,false,$brand->typeSearchBrand);
                if (!$result) {
                    //PROCEDEMOS A BUSCAR DE MANERA PARCIAL POR EL CARACTER 1
                    $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_1));
                    $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true,$brand->typeSearchBrand);
                    if (!$result) {
                        $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_1, strlen(trim($brand->name))));
                        $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true,$brand->typeSearchBrand);
                    }
                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 2
                    $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_2));
                    $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true,$brand->typeSearchBrand);
                    if (!$result) {
                        $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_2, strlen(trim($brand->name))));
                        $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true,$brand->typeSearchBrand);
                    }
                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 3
                    $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_3));
                    $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true,$brand->typeSearchBrand);
                    if (!$result) {
                        $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_3, strlen(trim($brand->name))));
                        $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true,$brand->typeSearchBrand);
                    }
                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 4
                    $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_4));
                    $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true,$brand->typeSearchBrand);
                    if (!$result) {
                        $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_4, strlen(trim($brand->name))));
                        $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true,$brand->typeSearchBrand);
                    }
                    //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 5
                    $txtPartialIni = substr($brand->name, 0, strpos(trim($brand->name), $this->caracter_5));
                    $result = $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialIni,true,$brand->typeSearchBrand);
                    if (!$result) {
                        $txtPartialFinal = substr($brand->name, strpos(trim($brand->name), $this->caracter_5, strlen(trim($brand->name))));
                        $this->searchBrandByName($this->extractionHeader->id,$brand->code,$brand->name,$txtPartialFinal,true,$brand->typeSearchBrand);
                    }
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
            DB::table('extraction_header')->where('id', $this->extractionHeader->id)->update(array('totalFound' => $totalFounded,'totalPartialFound' => $totalPartialFound,'totalMissing' => $totalNotFound));
        }
    }

    function processMissingArticleBrand(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::::::: regularizamos_articulos_por_marca ::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstBrandFounded = DB::table("extraction_subida")->selectRaw('DISTINCT(marca) as codMarca')->where('extractionHeaderId', $this->extractionHeader->id)->get();
        print_r("::::::::::::: TOTAL DE MARCAS : ".count($lstBrandFounded)." :::::::::::::::::"."\n" );
        if (count($lstBrandFounded)>0) {
            foreach ($lstBrandFounded as $b => $brand) {
                //print_r("fila articulo: ".$b." - Marca: ".$brand->codMarca."\n" );
                //if ($brand->codMarca != '001') {
                print_r("::::::::::::: fila marca : ".$b."- MARCA:". $brand->codMarca." :::::::::::::::::"."\n" );
                $this->searchArticle($brand->codMarca);
                //}
            }
        }

        //actualizamos los registros que no se pudieron encontrar
        print_r(":::::::::::::: ACTUALIZAMOS LOS REGISTROS NO ENCONTRADOS::::::::::::\n" );
        DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('statusArticle', 'CHARGED')
                                    ->update(array('statusArticle' => 'NOT_FOUND'));
    }

    function processMissingCustomer(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::::::::: regularizamos_importadores :::::::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstCustomer = DB::table("extraction_subida")->selectRaw('DISTINCT(TRIM(codTributario)) as customer')->where('extractionHeaderId', $this->extractionHeader->id)->get();
        if (count($lstCustomer) > 0) {
            //var_dump($lstCustomer);
            foreach ($lstCustomer as $c => $customer) {
                /*
                $response = Http::post($this->urlBase .'customers/getCustomerByIdentification', [
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
                */
                print_r("fila customer: ".$c." documento: ".$customer->customer."\n" );
                $objCustomer = json_decode(file_get_contents($this->urlBase .'customers/getCustomerByIdentification/'.$customer->customer));
                if (!is_null($objCustomer) && is_object($objCustomer)) {
                    DB::table('extraction_subida')->where('codTributario', $customer->customer)->update(array('statusImporter' => 'FOUNDED', 'codImporter' => $objCustomer->customer_code));
                }
            }
        }
        //actualizamos los registros que no se pudieron encontrar
        print_r(":::::::::::::: ACTUALIZAMOS LOS REGISTROS NO ENCONTRADOS::::::::::::\n" );
        DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('statusImporter', 'CHARGED')
                                    ->update(array('statusImporter' => 'NOT_FOUND'));

    }

    function processMissingProviderOld(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::: regularizamos_proveedores :::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstProviders = DB::table("extraction_subida")->selectRaw('DISTINCT(TRIM(embarcadorExportador)) as provider')
                                                    ->where('extractionHeaderId', $this->extractionHeader->id)
                                                    ->whereIn('statusProvider',['CHARGED','PARTIAL_FOUND'])
                                                    ->where('isActive',1)
                                                    ->where('isDeleted',0)
                                                    ->get();
        if (count($lstProviders) > 0) {
            foreach ($lstProviders as $p => $provider) {
                print_r("fila proveedores : ".$p." razon social:".$provider->provider ."\n" );
                $response = Http::post($this->urlBase .'general/getProviders', [
                    'provName' => $provider->provider
                ])->json();
                var_dump($response);
                //$this->searchProvider($lista->idproveedor,trim($lista->razonsocial),trim($lista->razonsocial),false);
            }
        }
    }

    function processMissingProvider(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r(":::::: regularizamos_proveedores_importadores :::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        //$lstProviders = json_decode(file_get_contents($this->urlBase .'general/getProviders'));
        $lstProviders = Http::post($this->urlBase .'general/getProviders')->body();
        $lstProviders = json_decode($lstProviders);
        if ($lstProviders->total_registros > 0) {
            foreach ($lstProviders->lista as $l => $lista) {
                print_r("fila proveedores : ".$l."\n" );
                //if ($lista->idproveedor != "010328") { // arreglar razon social
                    //$this->searchImporter($lista->idproveedor,trim($lista->razonsocial),trim($lista->razonsocial),false); CLIENTES
                    $this->searchProvider($lista->idproveedor,trim($lista->razonsocial),trim($lista->razonsocial),false);
                //}
            }
        }

        //actualizamos los registros que no se pudieron encontrar
        print_r(":::::::::::::: ACTUALIZAMOS LOS REGISTROS NO ENCONTRADOS::::::::::::\n" );
        DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('statusProvider', 'CHARGED')
                                    ->update(array('statusProvider' => 'NOT_FOUND'));
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

    function processArticleNotFound(){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizamos_articulos_no_econtrador :::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        $lstBrandFounded = DB::table("extraction_subida")->selectRaw('marca as codMarca,nameMarca')
                            ->where('extractionHeaderId', $this->extractionHeader->id)
                            ->groupBy('marca')
                            ->groupBy('nameMarca')->get();
        print_r("::::::::::::: TOTAL DE MARCAS : ".count($lstBrandFounded)." :::::::::::::::::"."\n" );
        if (count($lstBrandFounded)>0) {
            foreach ($lstBrandFounded as $b => $brand) {
                //print_r("fila articulo: ".$b." - Marca: ".$brand->codMarca."\n" );
                //if ($brand->codMarca != '001') {
                print_r("::::::::::::: fila marca : ".$b."- MARCA:". $brand->codMarca." :::::::::::::::::"."\n" );
                $this->searchArticleNotFound($brand->codMarca,$brand->nameMarca);
                //}
            }
        }

        //actualizamos los registros que no se pudieron encontrar
    }

    function searchBrandByName($extractionHeader,$brand_code,$brand_name_ini,$brand_to_search,$is_partial=false,$brand_to_typeSearchBrand){
        if (strlen(trim($brand_to_search))> 1) {
            // print_r("search codbrand: ".$brand_code." namebrand:".$brand_to_search."\n" );
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$brand_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    (LOCATE("'.$brand_to_search.'", UPPER(descripcionComercial)) + CHAR_LENGTH("'.$brand_to_search.'") - 1) as FINAL_WORD,
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
                                    ->where('isActive',1)
                                    ->where('isDeleted',0)
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($brand_to_search)).'%"')
                                    ->get();
            if(count($listFounded)>0){
                return $this->validateBrandFounded($listFounded,$brand_code,$brand_name_ini,$brand_to_search,$is_partial,$brand_to_typeSearchBrand);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function validateBrandFounded($listFounded,$brand_code,$brand_name_ini,$brand_to_search,$is_partial,$brand_to_typeSearchBrand){
        $status_founded=false;
        $result = false;
        foreach ($listFounded as $lst => $found) {
            //print_r("caracter anterior: ".$found->CARACTER_ANTERIOR." - validacion:  ".ctype_alpha($found->CARACTER_ANTERIOR)."\n" );
            if(!ctype_alpha($found->CARACTER_ANTERIOR)){
                switch($brand_to_search){
                    case (strtoupper(trim($found->FINAL_ESPACIO))):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_COMA))):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_GUION))):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_PARENTESIS))):
                        $status_founded=true;
                        break;
                    case (strtoupper(trim($found->FINAL_CORCHETES))):
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
                        // print_r("partials brand: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand_code, 'nameMarca' => $brand_name_ini, 'typeFoundColor' => 'badge bg-warning'));
                        $result = false;
                    }else{
                        // print_r("FOUNDED: ".$brand_to_search."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('status' => 'FOUNDED', 'marca' => $brand_code, 'nameMarca' => $brand_name_ini, 'typeFoundColor' => 'badge bg-success'));
                        $result = true;
                    }
                }/*else{
                    if (!$is_partial) {
                        switch($brand_to_typeSearchBrand){
                            case (strtoupper(trim($found->FINAL_ESPACIO))):
                                $status_founded=true;
                                break;
                            case (strtoupper(trim($found->FINAL_COMA))):
                                $status_founded=true;
                                break;
                            case (strtoupper(trim($found->FINAL_GUION))):
                                $status_founded=true;
                                break;
                            case (strtoupper(trim($found->FINAL_PARENTESIS))):
                                $status_founded=true;
                                break;
                            case (strtoupper(trim($found->FINAL_CORCHETES))):
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
                            //print_r("SEGUNDA BUSQUEDA ENCONTRADA: ".$brand_to_search."\n" );
                            DB::table('extraction_subida')->where('id', $found->id)->update(array('status' => 'FOUNDED', 'marca' => $brand_code, 'nameMarca' => $brand_name_ini, 'typeFoundColor' => 'badge bg-success'));
                            $result = true;
                        }
                    }
                }*/
            }
        }
        return $result;
    }

    function searchArticle($codeBrand){
        if (strlen(trim($codeBrand))>0) {
            $lstArticleBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getProductsByTrademark/'.$codeBrand));
            if (count($lstArticleBrand->data)>0) {
                foreach ($lstArticleBrand->data as $d => $brand) {
                    if (count($lstArticleBrand->data) == ($d + 1) ) {
                        print_r("TOTAL DE ARTICULOS: ".$d." -> ARTICULO : ".$brand->factory_code." codigo marca:".$codeBrand."\n" );
                    }
                    //print_r("fila articulo : ".$brand->factory_code." codigo marca:".$codeBrand."\n" );
                    //buscamos el articulo tal cual
                    $result = $this->searchArticleByCodeBrand($codeBrand,trim($brand->factory_code),trim($brand->factory_code),false);
                    if (!$result) {
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
    }

    function searchArticleByCodeBrand($brand_code,$article_ini,$article_to_search,$is_partial){
        $article_to_search = str_replace('"', '', $article_to_search);
        if (strlen(trim($article_to_search))> 3) {
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    (LOCATE("'.$article_to_search.'", UPPER(descripcionComercial)) + CHAR_LENGTH("'.$article_to_search.'") - 1) as FINAL_WORD,
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
                // print_r("brand: ".$brand_code." ArticuloInicial: ".$article_ini." articuloSearch: ".$article_to_search."\n" );
                return $this->validateArticleFounded($listFounded,$article_ini,$article_to_search,$is_partial);
            }
            return false;
        }else{
            return false;
        }
    }

    function validateArticleFounded($listFounded,$article_ini,$article_to_search,$is_partial){
        $result = false;
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
                        // print_r("CAMBIAMOS ESTADO DE ARTICULO A BUSCAR: ".$article_to_search." Y EL ARTICULO PARCIAL:".$article_ini."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'PARTIAL_FOUND','status' => 'FOUNDED','codigo' => $article_ini,'typeFoundArticle' => 'badge bg-warning'));
                        $result = false;
                    }else{
                        // print_r("CAMBIAMOS ESTADO DE ARTICULO A BUSCAR: ".$article_to_search." Y EL ARTICULO COMPLETO:".$article_ini."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'FOUNDED','status' => 'FOUNDED','codigo' => $article_ini,'typeFoundArticle' => 'badge bg-success'));
                        $result = true;
                    }
                }
            }
        }
        return $result;
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
                                    (LOCATE("'.$importer_to_search.'", UPPER(importador)) + CHAR_LENGTH("'.$importer_to_search.'") - 1) as FINAL_WORD,
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
        $provider_to_search = str_replace('"', '', $provider_to_search);
        if (strlen(trim($provider_to_search))> 0) {
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(embarcadorExportador),
                                    substring(UPPER(embarcadorExportador),LOCATE("'.$provider_to_search.'",embarcadorExportador)-1,1) AS CARACTER_ANTERIOR,
                                    "'.$provider_to_search.'" AS provider_to_search,
                                    LOCATE("'.$provider_to_search.'",UPPER(embarcadorExportador)) as POSICION,
                                    (LOCATE("'.$provider_to_search.'", UPPER(embarcadorExportador)) + CHAR_LENGTH("'.$provider_to_search.'") - 1) as FINAL_WORD,
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
                print_r("CODIGO: ".$codigo." providerInicial: ".$provider_ini." providerSearch: ".$provider_to_search."\n" );
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

    function searchArticleNotFound($codeBrand,$nameMarca){
        if (strlen(trim($codeBrand))>0) {
            $lstArticleBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getProductsByTrademark/'.$codeBrand));
            if (count($lstArticleBrand->data)>0) {
                foreach ($lstArticleBrand->data as $d => $brand) {
                    if (count($lstArticleBrand->data) == ($d + 1) ) {
                        print_r("TOTAL DE ARTICULOS: ".$d." -> ARTICULO : ".$brand->factory_code." codigo marca:".$codeBrand."\n" );
                    }
                    print_r("fila: ".($d+1)." articulo : ".$brand->factory_code." codigo marca:".$codeBrand."\n" );
                    //buscamos el articulo tal cual
                    $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,trim($brand->factory_code),trim($brand->factory_code),false);
                    $result = true;
                    if (!$result) {
                        //PROCEDEMOS A BUSCAR DE MANERA PARCIAL POR EL CARACTER 1
                        $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_1));
                        if (strlen(trim($txtPartialIni))>3) {
                            $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialIni,true);
                            if (!$result) {
                                $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_1, strlen(trim($brand->factory_code))));
                                if (strlen(trim($txtPartialFinal))>3) {
                                    $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialFinal,true);
                                }
                            }
                        }

                        //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 2
                        $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_2));
                        if (strlen(trim($txtPartialIni))>3) {
                            $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialIni,true);
                            if (!$result) {
                                $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_2, strlen(trim($brand->factory_code))));
                                if (strlen(trim($txtPartialFinal))>3) {
                                    $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialFinal,true);
                                }
                            }
                        }

                        //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 3
                        $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_3));
                        if (strlen(trim($txtPartialIni))>3) {
                            $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialIni,true);
                            if (!$result) {
                                $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_3, strlen(trim($brand->factory_code))));
                                if (strlen(trim($txtPartialFinal))>3) {
                                    $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialFinal,true);
                                }
                            }
                        }

                        //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 4
                        $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_4));
                        if (strlen(trim($txtPartialIni))>3) {
                            $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialIni,true);
                            if (!$result) {
                                $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_4, strlen(trim($brand->factory_code))));
                                if (strlen(trim($txtPartialFinal))>3) {
                                    $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialFinal,true);
                                }
                            }
                        }

                        //PROCEDEMOS A BUCAR DE MANERA PARCIAL POR EL CARACTER 5
                        $txtPartialIni = substr($brand->factory_code, 0, strpos(trim($brand->factory_code), $this->caracter_5));
                        if (strlen(trim($txtPartialIni))>3) {
                            $result = $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialIni,true);
                            if (!$result) {
                                $txtPartialFinal = substr($brand->factory_code, strpos(trim($brand->factory_code), $this->caracter_5, strlen(trim($brand->factory_code))));
                                if (strlen(trim($txtPartialFinal))>3) {
                                    $this->searchArticleByCodeBrandNotFound($nameMarca,$codeBrand,$brand->factory_code,$txtPartialFinal,true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function searchArticleByCodeBrandNotFound($nameMarca,$brand_code,$article_ini,$article_to_search,$is_partial){
        $article_to_search = str_replace('"', '', $article_to_search);
        if (strlen(trim($article_to_search))> 3) {
            /*
            $first = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    (LOCATE("'.$article_to_search.'", UPPER(descripcionComercial)) + CHAR_LENGTH("'.$article_to_search.'") - 1) as FINAL_WORD,
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
                                    ->where('marca','!=',$brand_code)
                                    ->where('status','FOUNDED')
                                    ->where('statusArticle','NOT_FOUND')
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($article_to_search)).'%"');
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    (LOCATE("'.$article_to_search.'", UPPER(descripcionComercial)) + CHAR_LENGTH("'.$article_to_search.'") - 1) as FINAL_WORD,
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
                                    ->where('marca','!=',$brand_code)
                                    ->where('status','PARTIAL_FOUND')
                                    ->where('statusArticle','NOT_FOUND')
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($article_to_search)).'%"')
                                    ->union($first)
                                    ->get();*/
            $listFounded = DB::table('extraction_subida')
                                    ->selectRaw('
                                    id,
                                    UPPER(descripcionComercial),
                                    substring(UPPER(descripcionComercial),LOCATE("'.$article_to_search.'",descripcionComercial)-1,1) AS CARACTER_ANTERIOR,
                                    (LOCATE("'.$article_to_search.'", UPPER(descripcionComercial)) + CHAR_LENGTH("'.$article_to_search.'") - 1) as FINAL_WORD,
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
                                    ->where('marca','!=',$brand_code)
                                    //->whereIn('status',['FOUNDED','PARTIAL_FOUND'])
                                    ->whereRaw('(status="FOUNDED" OR status="PARTIAL_FOUND")')
                                    ->where('statusArticle','NOT_FOUND')
                                    ->whereRaw('UPPER(TRIM(descripcionComercial)) like "%'.strtoupper(trim($article_to_search)).'%"')
                                    ->get();
            //dd($listFounded);
            if(count($listFounded)>0){
                // print_r("brand: ".$brand_code." ArticuloInicial: ".$article_ini." articuloSearch: ".$article_to_search."\n" );
                return $this->validateArticleNotFound($nameMarca,$brand_code,$listFounded,$article_ini,$article_to_search,$is_partial);
            }
            return false;
        }else{
            return false;
        }
    }

    function validateArticleNotFound($nameMarca,$brand_code,$listFounded,$article_ini,$article_to_search,$is_partial){
        $result = false;
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
                        // print_r("CAMBIAMOS ESTADO DE ARTICULO A BUSCAR: ".$article_to_search." Y EL ARTICULO PARCIAL:".$article_ini."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'PARTIAL_FOUND','status' => 'FOUNDED','marca'=> $brand_code, 'nameMarca'=> $nameMarca,'codigo' => $article_ini,'typeFoundArticle' => 'badge bg-warning'));
                        $result = false;
                    }else{
                        // print_r("CAMBIAMOS ESTADO DE ARTICULO A BUSCAR: ".$article_to_search." Y EL ARTICULO COMPLETO:".$article_ini."\n" );
                        DB::table('extraction_subida')->where('id', $found->id)->update(array('statusArticle' => 'FOUNDED','status' => 'FOUNDED','marca'=> $brand_code, 'nameMarca'=> $nameMarca,'codigo' => $article_ini,'typeFoundArticle' => 'badge bg-success'));
                        $result = true;
                    }
                }
            }
        }
        return $result;
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
