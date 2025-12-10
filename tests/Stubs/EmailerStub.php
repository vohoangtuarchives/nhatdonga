<?php

class EmailerStub
{
    public array $sent = [];
    public function defaultAttrs()
    {
        return ['vars' => [], 'vals' => []];
    }
    public function addAttrs($arr, $def) { return $arr; }
    public function markdown($template) { return 'TEMPLATE'; }
    public function send($target, $arrayEmail, $subject, $message, $fileAttachment)
    {
        $this->sent[] = compact('target','arrayEmail','subject','message');
        return true;
    }
    public function set($k,$v){}
    public function get($k){ return null; }
}

