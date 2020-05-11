<?php

namespace Tests\Unit;

use App\Http\Controllers\FileInterpretationController;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use  Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Podlove\Webvtt\Parser;
use Podlove\Webvtt\ParserException;

class FileInterpretationTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /*public function testExample()
    {
        $this->assertTrue(true);
    }*/

    public function testSplitTextBlock()
    {
        $FileInterpretationController = new FileInterpretationController();
        $resultArray = ($FileInterpretationController->splitFile("/app/files/unit.vtt"));
        
        $expectedArray = array(
            0 => 
            ["start" => 9,
            "end" => 11,
            "text" => "We zijn in New York City",
            "identifier" => "",
            "voice" => "Roger Bingham"],

            1 => 
            ["start" => 11,
            "end" => 13,
            "text" => "We zijn in New York City",
            "identifier" => "",
            "voice" => "Roger Bingham"]
        );
        
        $this->assertEquals( $expectedArray, $resultArray);
    }

    /*public function testSplitFile()
    {
        $class = App::make('FileIneterpretaionController');
        $expectedArray = array(
            array(
                "Timestamp"=> "00:13.000 --> 00:16.000",
                "Naam"=> "<v Roger Bingham>",
                "Text"=> "We're actually at the Lucern Hotel, just down the street"     
            ),
            array(
                "Timestamp"=> "00:16.000 --> 00:18.000",
                "Naam"=> "<v Roger Bingham>",
                "Text"=> "from the American Museum of Natural History"
            )
    );
        $actualArray = $class->splitFile();
        //print_r($actualArray);
        $this->assertEquals($expectedArray, $actualArray);
    }*/
}
