<?php

    header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];

    $input = json_decode(file_get_contents('php://input'), true);

    switch ($method) {
        case 'POST':
            $json = json_encode(['message' => 'Not set.']);

            if(isset($_FILES['files'])) {
                $fileCount = count($_FILES['files']['name']);

                for ($i=0; $i < $fileCount; $i++) { 
                    $fileName = $_FILES['files']['name'][$i];
                    $file = $_FILES['files']['tmp_name'][$i];

                    $name = basename($file);

                    Setup();
                    move_uploaded_file($file, "data/$name.zip");

                    UnZipAndCopy($file);

                    $json = XmlToJson($name);

                    CleanUpDirectory("word-document/$name");
                    CleanUpFiles($name);

                    $result[] = $json;
                }
            }
            
            echo json_encode($result);

            break;
    }

    function UnZipAndCopy($file) {
        $zip = new ZipArchive();
        $name = basename($file);
        $zip -> open("data/$name.zip");

        if (!is_dir("word-document/$name")) {
            try {
                mkdir("word-document/$name");
                $zip -> extractTo("word-document/$name");
    
                copy("word-document/$name/word/document.xml", "xml-document/$name.xml");
    
                return true;
            } catch (Throwable $th) {
                return false;
            }
        }
    }

    function XmlToJson($fileName) {
        $xml = file_get_contents("xml-document/$fileName.xml");

        $xmlObj = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA, 'w', true);
        //print_r($xmlObj);

        return $xmlObj;
    }

    function CleanUpDirectory($dirName) {
        if (is_dir($dirName)) {
            $objects = scandir($dirName);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dirName.'/'.$object) == "dir") {
                        CleanUpDirectory($dirName.'/'.$object);
                    } else {
                        unlink($dirName.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dirName);
        }
    }

    function CleanUpFiles($name) {
        unlink("data/$name.zip");
        unlink("xml-document/$name.xml");
    }

    function Setup() {
        if (!is_dir('data')) {
            mkdir('data');
        }
        if (!is_dir('word-document')) {
            mkdir('word-document');
        }
        if (!is_dir('xml-document')) {
            mkdir('xml-document');
        }
    }
?>