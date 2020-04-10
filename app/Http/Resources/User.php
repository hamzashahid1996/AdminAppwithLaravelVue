<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //now here we will customize which data we need from table
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email'  =>$this->email,
            'role'=>$this->role->name,
            'phone_no'=>$this->phone_no,
            'photo'=> asset( $this->profile->photo),
            'created_at'=>$this->created_at->format('d-m-Y H:i:s'),
            'updated_at'=>$this->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}
