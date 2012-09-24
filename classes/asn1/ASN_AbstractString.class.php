<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 * 
 * Copyright © Friedrich Große, Berlin 2012
 * 
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PHPASN1;

abstract class ASN_AbstractString extends ASN_Object implements Parseable {
    
    private $allowedCharacters = array();
    
    /**
     * Creates a new ASN.1 PrintableString.
     * 
     * The ITU-T X.680 Table 8 permits the following characters:    
     * Latin capital letters A,B, ... Z
     * Latin small letters   a,b, ... z 
     * Digits                0,1, ... 9
     * SPACE                 (space)
     * APOSTROPHE            '
     * LEFT PARENTHESIS      (
     * RIGHT PARENTHESIS     )
     * PLUS SIGN             +
     * COMMA                 ,
     * HYPHEN-MINUS          -
     * FULL STOP             .
     * SOLIDUS               /
     * COLON                 :
     * EQUALS SIGN           =
     * QUESTION MARK         ? 
     * 
     */
    public function __construct($string) {          
        $this->value = $string;
    }
    
    protected function allowCharacter($character) {
        $this->allowedCharacters[] = $character;
    }
    
    protected function allowCharacters($character1, $character2=null, $characterN=null) {
        $characters = func_get_args();
        foreach ($characters as $character) {
            $this->allowedCharacters[] = $character;
        }
    }
    
    protected function allowNumbers() {
        for ($char='0'; $char <= '9' ; $char++) { 
            $this->allowedCharacters[] = $char;
        }
    }
    
    protected function allowAllLetters() {
        $this->allowSmallLetters();
        $this->allowCapitalLetters();
    }
    
    protected function allowSmallLetters() {
        for ($char='a'; $char <= 'z' ; $char++) { 
            $this->allowedCharacters[] = $char;
        }
    }
    
    protected function allowCapitalLetters() {
        for ($char='A'; $char <= 'Z' ; $char++) { 
            $this->allowedCharacters[] = $char;
        }
    }
    
    protected function allowSpaces() {
        $this->allowedCharacters[] = ' ';        
    }
        
    protected function calculateContentLength() {
        return strlen($this->value);
    }
    
    protected function getEncodedValue() {
        $this->checkString();
        return $this->value;
    }
        
    protected function checkString() {
        $stringLength = $this->getContentLength();
        for ($i=0; $i < $stringLength; $i++) {            
            if(in_array($this->value[$i], $this->allowedCharacters) == false) {
                $typeName = Identifier::getName(static::getType());
                throw new \Exception("Could not create a {$typeName} from the character sequence '{$this->value}'.");
            }
        }        
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {                
        self::parseIdentifier($binaryData[$offsetIndex], static::getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);        
        $string = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;
        
        $parsedObject = new self($string);
        $parsedObject->setContentLength($contentLength);        
        return $parsedObject;
    }
           
}
?>