<?php
namespace isv\Form;
use isv\DB\ModelBase;
/**
 * Class Form
 * @package isv\Form
 * @version 1.1
 */
class Form
{
    protected $formParams = ['method' => 'post', 'action' => '/'];
    protected $formFields;
    protected $exists=NULL;
    private $formArrayName=NULL;
    protected $model;

    /**
     * Form constructor.
     * @param $modelArray
     * @param $params
     * @param $model ModelBase
     * IN constructor write
     */
    public function __construct($modelArray, $params, $model=NULL)
    {
        $this->model = $model;
        if($modelArray[$modelArray['primaryName']]) {
            $this->exists = [
                'name' => $modelArray['primaryName'],
                'value'=> $modelArray[$modelArray['primaryName']]
            ];
        }
        foreach($modelArray as $k=>$v)
        {
            if( isset($params[$k]) )
            {
                $this->formFields[$k] = $params[$k];
                $this->formFields[$k]['value'] = $v;
            }
        }
    }

    public function fromArrayName($name)
    {
        $this->formArrayName = $name;
    }

    /**
     * show form field generated using this class build method
     * @param $name
     * @param null|array $params
     * @param null|array $selectors
     * @return string
     */
    public function show($name, $params=NULL, $selectors=NULL)
    {
        if($params)
        {
            foreach ($params as $param=>$value) {
                $this->formFields[$name][$param] = $value;
            }
        }
        return $this->build($name, $this->formFields[$name], $selectors);
    }

    public function value($name)
    {
        return isset( $this->formFields[$name]['value'] ) ? $this->formFields[$name]['value'] : FALSE;
    }

    /**
     * check to type
     * @param $name
     * @param $params
     * @param $selectors
     * @return bool|string
     */
    public function build($name, $params, $selectors=NULL)
    {
        if($this->formArrayName)
            $name = $this->formArrayName.'['.$name.']';
        if($params['type'] === 'textarea')
            return $this->getTextArea($name, $params);
        else if($params['type'] === 'checkbox')
            return $this->getCheckbox($name, $params);
        else if($params['type'] === 'radio')
            return $this->getRadio($name, $params);
        else if($params['type'] === 'hidden')
            return $this->getHidden($params);
        else if($params['type'] === 'select')
            return $this->getSelect($name, $params, $selectors);
        else if($params['type'] === 'password')
            return $this->getPassword($name, $params);
        else if($params['type'] === 'email')
            return $this->getEmail($name, $params);
        else if($params['type'] === 'tel')
            return $this->getTel($name, $params);
        else if($params['type'] === 'color')
            return $this->getColor($name, $params);
        else if($params['type'] === 'datetime')
            return $this->getDateTime($name, $params);
        else if($params['type'] === 'number')
            return $this->getNumberInput($name, $params);
        else
            return $this->getInput($name, $params);
    }

    /**
     * creating checkboxes as array k=>$v
     * @param $name
     * @param $params
     * @return string
     */
    public function getCheckbox($name, $params)
    {
        $checked = ($params['value']) ? 'checked="checked"' : '';
        $str = '<input type="checkbox" '.$checked.'  name="'.$name.'" ';
        foreach ($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str .=' />';
        return $str;
    }

    public function getSelect($name, $params, $selectors)
    {
        unset($params['type']);
        $str = '<select name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>'."\r\n";
        foreach($selectors as $value=>$option)
        {
            if( isset($params['value']) && $params['value'] == $value )
                $str.='<option selected="selected" value="'.$value.'">'.$option.'</option>'."\r\n";
            else
                $str.='<option value="'.$value.'">'.$option.'</option>'."\r\n";
        }
        $str.='</select>';
        return $str;
    }

    /**
     * creating radio as array k=>$v
     * @param $name
     * @param $params
     * @return string
     */
    public function getRadio($name, $params)
    {
        $str = '';
        foreach ($params as $k=>$v){
            $str = '<input type="radio" name="'.$name.'" value="'.$v.'" />';
        }
        return $str;
    }

    /**
     * text area input field
     * @param $name
     * @param $params
     * @return string
     */
    public function getTextArea($name, $params)
    {
        $val = isset($params['value']) ? $params['value'] : NULL;
        $str = '<textarea name="'.$name.'" ';
        foreach($params as $k=>$v){
            if($k!='value')
                $str .=$k.'="'.$v.'" ';
        }
        $str.='>'.$val.'</textarea>';
        return $str;
    }

    /**
     * simple text input
     * @param $name
     * @param $params
     * @return string
     */
    public function getInput($name, $params)
    {
        $str = '<input type="text" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * Number input
     * @param $name
     * @param $params
     * @return string
     */
    public function getNumberInput($name, $params)
    {
        $str = '<input type="number" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * Date & time picker default from browser
     * @param $name
     * @param $params
     * @return string
     */
    public function getDateTime($name, $params)
    {
        $str = '<input type="date" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * password input
     * @param $name
     * @param $params
     * @return string
     */
    public function getPassword($name, $params)
    {
        $str = '<input type="password" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * password input
     * @param $name
     * @param $params
     * @return string
     */
    public function getEmail($name, $params)
    {
        $str = '<input type="email" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * password input
     * @param $name
     * @param $params
     * @return string
     */
    public function getTel($name, $params)
    {
        $str = '<input type="tel" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    public function getColor($name, $params)
    {
        $str = '<input type="color" name="'.$name.'" ';
        foreach($params as $k=>$v){
            $str.=$k.'="'.$v.'" ';
        }
        $str.='/>';
        return $str;
    }

    /**
     * hidden field required to transfer this method
     * params array key pairs name and value
     * @param $params
     * @return string
     */
    public function getHidden($params)
    {
        return '<input type="hidden" name="'.$params['name'].'" value="'.$params['value'].'" />';
    }

    /**
     * Submit button
     * @param null $params
     * @return string
     */
    public function submit($params=NULL)
    {
        $str = '<input type="submit" ';
        if($params){
            foreach($params as $k=>$v)
            {
                $str.=$k.'="'.$v.'" ';
            }
        }
        return $str.' />';
    }

    /**
     * Form HTML tag start with selectors transfer in params array
     * @param null $params
     * @return string
     */
    public function start($params=NULL)
    {
        if($params){
            foreach($params as $k=>$v){
                $this->formParams[$k] = $v;
            }
        }
        $begin = '<form ';
        foreach($this->formParams as $param=>$value){
            $begin .=$param.'="'.$value.'" ';
        }
        $begin .='>';
        return $begin;
    }

    /**
     * Form close tag and adding hidden input with model primary key id
     * for loaded models
     * @return string
     */
    public function end()
    {
        $str = ($this->exists) ? $this->getHidden($this->exists) : '';
        return $str."\r\n".'</form>';
    }

    public function isExists()
    {
        return $this->exists === NULL ? false : true;
    }

    public function label($name)
    {
        return $this->model->label($name);
    }
}