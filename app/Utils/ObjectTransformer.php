<?php
namespace app\Utils;

class ObjectTransformer{
    public function __construct()
    {
        $this->custom_request = [];
        $this->custom_requests = [];
    }
    public function getTransformedObject($obj, $type) 
    {
        foreach($obj as $value){
            $this->custom_request['request_type'] = $type;
            $this->custom_request['name'] = $value->name;
            $this->custom_request['created_at'] = $value->created_at;
            $this->custom_request['id'] = $value->id;
        }
        return $this->custom_request;
    }
}