<?php

namespace App\Imports;

use App\Models\Admin\ExtractionArticlesModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ArticlesBrandImport implements ToModel,WithStartRow
{
    public function __construct(string $responsable, string $brandId)
    {
        $this->responsable = $responsable;
        $this->brandId = $brandId;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        return ExtractionArticlesModel::updateOrCreate(
            [
                'brandId' => $this->brandId,
                'factory_code' => trim($row['2'])
            ],
            [
            'brandId'       => $this->brandId,
            'code'          => trim($row['0']),
            'name'          => trim($row['1']),
            'factory_code'  => trim($row['2']),
            'status'        => trim($row['3']),
            'is_active'     => 1,
            'is_deleted'    => 0
        ]);
    }

    public function transformNameToId($value) {
        $brand = ExtractionArticlesModel::where('code', trim($value))->first();
    
        if(!$brand){
            return null;
        }
    
        return $brand->id;
    }
}
