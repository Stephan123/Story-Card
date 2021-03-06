<?php
/**
 * General classes
 * Define a number of key classes for the StoryCard System
 *
 * @author Carl Saggs
 * @version 0.2
 * @licence MIT
 */

//Config
class Config{
	private static $data;
	public static function store($data){self::$data = $data;}
	public static function get($ref){return self::$data[$ref];}
	public static function set($ref,$val){self::$data[$ref]=$val;}
}
//Auth driver abstract
abstract class AuthAbstract
{
    //abstract methods
    abstract public function login($username,$password);
    abstract public function logout();
}
//Data (CardStore) driver abstract
abstract class StoreAbstract
{
    //abstract methods
	abstract public function getCardsFor($product, $sprint=0);
    abstract public function listProducts();
    abstract public function addCard($data);
    abstract public function updateCard($id, $data);
    abstract public function updateCards($cards);
    abstract public function removeCard($id);
    abstract public function addProduct($title, $data);
    abstract public function addSprint($identifier, $data);
    abstract public function getSprints($product);
    abstract public function setup();

    //Remap (utility method)
    //Convert system attributes to datastore attributes
    public function reMap($data, $toInternal=false){

        //Return type should be the same as the one passed in.
        $isobj = is_object($data);

        //get column mappings (internal=>external)
        $map = Config::get("column_mappings");
        //If converting the other way, just flip the mapping array round
        if($toInternal) $map = array_flip($map); 
        //Create new Object
        $newData = new stdClass;
        //For each attribute in passed in data
        //Add it to the new object with the attribute name found via the mapping config.
        //Data that does not have a match in the mapping data will be ignored.
        foreach($data as $attr=>$val){
            if(isset($map[$attr])) $newData->{$map[$attr]} = $val;
        }
        //Return remapped object. (as array if that is what is was to begin with)
        return ($isobj) ? $newData : (array)$newData;
    }

    //Show error to user (for critical errors)
    protected function showError($errorMessage){
        echo json_encode(array("setup"=>true, "error"=>true, "message"=>$errorMessage));
        die();
    }
}