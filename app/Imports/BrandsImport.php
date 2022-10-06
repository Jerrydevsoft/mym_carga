<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Admin\ExtractionBrandModel;

class BrandsImport implements ToModel,WithStartRow
{

    public function __construct(string $responsable)
    {
        $this->responsable = $responsable;
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
        return ExtractionBrandModel::updateOrCreate(
            [
                'code' => trim($row['0'])
            ],
            [
            'code'          => trim($row['0']),
            'name'          => trim($row['1']),
            'lengthName'    => 0,
            'status'        => 1,
            'is_active'     => 1,
            'is_deleted'    => 0
        ]);
    }

    public function transformNameToId($value) {
        $brand = ExtractionBrandModel::where('code', trim($value))->first();
    
        if(!$brand){
            return null;
        }
    
        return $brand->id;
    }
}
