<?php

namespace Php247\Php247;
use Php247\Db\DatabaseHandler;
use Zend\Validator\Identical, Zend\Validator\NotEmpty;
class Php247
{

    // Private constructor to prevent direct creation of object
    /**
     * Currencyconverter::__construct()
     * 
     * @return
     */
    private function __construct()
    {

    }
    /**
     * Currencyconverter::sql_load_currency_default()
     * Used to load from database currencies codes list
     * @return :currencies codes array 
     */
    public static function sql_load_currency_default()
    {
        $sql = 'CALL sql_load_currency_default()';
        $result = DatabaseHandler::GetAll($sql);
        DatabaseHandler::Close();
        return $result;
    }
    /**
     * Currencyconverter::sql_load_currency_all()
     * used to generate all possible currencies used
     * @return currency(name, code)
     */
    public static function sql_load_currency_all()
    {
        $sql = 'CALL sql_load_currency_all()';
        $result = DatabaseHandler::GetAll($sql);
        DatabaseHandler::Close();
        return $result;
    }
    /**
     * Currencyconverter::safeInput()
     * 
     * @return the proper usable input as needed int the application
     */
    public static function safeInput($input)
    {
        $d1 = "'";
        $d2 = '"';
        $value = str_replace(array(
            $d1,
            $d2,
            $d1,
            $d2), array(
            "&#39;",
            "&quot;",
            "&#39;",
            "&quot;"), $input);
        return trim($value);
    }
    //Factory validate and return valid data from database

    /**
     * Currencyconverter::getConvertionRatePerUnit()
     * 
     * @return error or currency converted
     */
    public static function getConvertionRatePerUnit($getbasecurrency, $gettargetcurrency,
        $convertionamount)
    {
        $errorMessage = '';
        $NotEmpty = new NotEmpty();
        $resultbase = $NotEmpty->isValid($getbasecurrency);
        $resulttarget = $NotEmpty->isValid($gettargetcurrency);
        $resultamout = $NotEmpty->isValid($convertionamount);
        $notEmptyArrray = array(
            $resultbase,
            $resulttarget,
            $resultamout);
        switch ($notEmptyArrray)
        {
            case array(
                    true,
                    true,
                    true):
                $Identical = new Identical($getbasecurrency);
                if ($Identical->isValid($gettargetcurrency))
                {
                    $errorMessage .= 'Failed: Please select differente currencies';
                } else
                {
                    $isFloat = filter_var($convertionamount, FILTER_VALIDATE_FLOAT);
                    if ($isFloat != false)
                    {
                        $sql = 'CALL sql_get_currency_convert(:inbasecurrency,:intargetcurrency)';
                        $params = array(':inbasecurrency' => $getbasecurrency, ':intargetcurrency' => $gettargetcurrency);
                        $result = DatabaseHandler::GetAll($sql, $params);
                        DatabaseHandler::Close();
                        if (!empty($result[0]['rateperunit']))
                        {
                            $converted = self::getCurrencysSymbol($gettargetcurrency)
                                         .$result[0]['rateperunit'] * $convertionamount;
                            $errorMessage .= $converted;
                        } else
                        {
                            $errorMessage .= 'Rate/Unit not available, please add more rate!';
                        }

                    } else
                    {
                        $errorMessage .= 'The amount to convert must be valid! "x.xxxx" or "xxxxx" and Greater to Zero';
                    }
                }
                break;
            default:
                $errorMessage .= 'Please all input are required:**';
                break;
        }
        return $errorMessage;
    }
    // 
      
                            
    /**
     * Currencyconverter::addMoreCurrencies()
     * 
     * @return message(error, success)
     */
    public static function addMoreCurrencies(        
        $basecurrencyValue, 
        $basecurrencyText,
        $gettargetcurrencyValue,
        $gettargetcurrencyText,
        $convertionamount,
        $getbasecurrencyall,
        $gettargetcurrencyall
        )
        {
           $errorMessage = '';
        $NotEmpty = new NotEmpty();
        $resultbase = $NotEmpty->isValid($getbasecurrencyall);
        $resulttarget = $NotEmpty->isValid($gettargetcurrencyall);
        $resultamout = $NotEmpty->isValid($convertionamount);
        $notEmptyArrray = array(
            $resultbase,
            $resulttarget,
            $resultamout);
        switch ($notEmptyArrray)
        {
            case array(
                    true,
                    true,
                    true):
                $Identical = new Identical($basecurrencyValue);
                if ($Identical->isValid($gettargetcurrencyValue))
                {
                    $errorMessage .= 'Failed: Please select differente currencies';
                } else
                {
                    $isFloat = filter_var($convertionamount, FILTER_VALIDATE_FLOAT);
                    if ($isFloat != false)
                    {
                        $sql = 'CALL sql_get_currency_convert(:inbasecurrency,:intargetcurrency)';
                        $params = array(':inbasecurrency' => $basecurrencyValue, ':intargetcurrency' => $gettargetcurrencyValue);
                        $result = DatabaseHandler::RowNum($sql, $params);
                        DatabaseHandler::Close();
                        if (empty($result))
                        {  //sql_add_more_currencies
                            
                            $message = self::saveNewCurrencyRate(
                                          $basecurrencyValue,
                                          $gettargetcurrencyValue,
                                          $convertionamount
                                          );
                            $errorMessage .= $message .$result;
                        } else
                        {
                            $errorMessage .= 'Please, the rate to add is already inside!';
                        }

                    } else
                    {
                        $errorMessage .= 'The amount to convert must be valid! "x.xxxx" or "xxxxx" and Greater to Zero';
                    }
                }
                break;
            default:
                $errorMessage .= 'Please all input are required:**';
                break;
        }
        return $errorMessage;  
        }
     
     /**
      * Currencyconverter::saveNewCurrencyRate()
      * 
      * @return message(success)
      */
     private static function saveNewCurrencyRate($inbasecurrency,
                                          $intargetcurrency,
                                          $intargetcurrencyrate
                                          )
        {
        $sql = 'CALL sql_add_more_currencies(:inbasecurrency,:intargetcurrency,:intargetcurrencyrate)';
        $params = array(':inbasecurrency' => $inbasecurrency,
                         ':intargetcurrency' => $intargetcurrency,
                         ':intargetcurrencyrate' => $intargetcurrencyrate
                         );
        $result = DatabaseHandler::Execute($sql, $params);
        DatabaseHandler::Close();
        return 'Sussess';
     }
     /**
      * Currencyconverter::updateCurrencybaseList()
      * 
      * @return message(error in database if is set)
      */
     public static function updateCurrencybaseList($currency,$currency_name)
     {
        if(!empty($currency) && !empty($currency_name))
        {
        $sql= 'CALL sql_updateCurrencybaseList(:inbasecurrency,:inbasecurrencyname)';
        $params = array(':inbasecurrency' => $currency, ':inbasecurrencyname' => $currency_name);
        $result = DatabaseHandler::Execute($sql, $params);
        DatabaseHandler::Close();   
        }
       
     }
     /**
      * Currencyconverter::load_default_currencies_rate_list()
      * used to generate the of currencies having initial currency rate per unit
      * @return currency(code,name)
      */
     public static function load_default_currencies_rate_list()
     {      
        $sql= 'CALL sql_load_default_currencies_rate_list()';
        $result = DatabaseHandler::GetAll($sql);
        DatabaseHandler::Close();   
       return $result;
     }
     
     /**
      * Currencyconverter::editCurrencyRate()
      * used to edit the currency default rate per unit
      * @return message(error, success)
      */
     public static function editCurrencyRate($basecurrency,$targetcurrency,$rate)
     {  $errorMessage = '';
        $isFloat = filter_var($rate, FILTER_VALIDATE_FLOAT);
                    if ($isFloat != false)
                    {
                        $sql = 'CALL sql_update_currency_rate(:inbasecurrency,:intargetcurrency,:inratecurrency)';
                        $params = array(':inbasecurrency' => $basecurrency,
                                        ':intargetcurrency' => $targetcurrency,
                                        ':inratecurrency' => $rate
                                        );
                        $result = DatabaseHandler::Execute($sql,$params);
                        DatabaseHandler::Close();
                        $errorMessage .='Update: success';                      
                    }
                    else{
                        $errorMessage .= 'The amount to convert must be valid! "x.xxxx" or "xxxxx" and Greater to Zero';
                    }
        return $errorMessage;
     }
     //
     /**
      * Currencyconverter::getCurrencysSymbol()
      * 
      * @return currency symbole given the currency code
      */
     final private static function getCurrencysSymbol($currencyCode)
     {  
       $sql = 'CALL get_currency_symbol(:incurrencycode)';
       $params = array(':incurrencycode' =>$currencyCode );
       $request = DatabaseHandler::GetAll($sql,$params);
       $response =$request[0]['csymbole'];
       DatabaseHandler::Close();
       return $response;
     }
          
}
