<?php
/**
 * Created by PhpStorm.
 * User: veerajshenoy
 * Date: 29/10/18
 * Time: 11:01 AM
 */

require __DIR__ . '../../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Rekognition\RekognitionClient;

class PHPAWS {
    private $options;
    private $rekognition;
    private $s3;

    public function __construct() {
        $this->options = [
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => array(
                'key' => 'AKIAIHYHT6XSCKTQYCDA',//Add key here
                'secret' => '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', //Add secret here
            )
        ];

        //Initiate an Amazon reKognition
        $this->rekognition = new RekognitionClient($this->options);

        // Instantiate an Amazon S3 client.
        $this->s3 = new S3Client($this->options);
    }

    /*
     * Function to upload images to bucket
     */
    function uploadimagetos3(string $fileimage, string $bucketname, string $ext) {
        $imagename = uniqid() . '.' . $ext;
        try {
            $reponse = $this->s3->putObject([
                'Bucket' => $bucketname,
                'Key' => $imagename,
                'Body' => $this->imageread($fileimage)
            ]);
            $reponse['@metadata']['imagename'] = $imagename;
            return $reponse['@metadata'];
        } catch (Aws\S3\Exception\S3Exception $e) {
            return array('statusCode' => 500);
        }
    }


    /*
     * to create a collection
     */
    function createorconnecttoCollection(string $collectionName) {
        $this->rekognition->createCollection([
            'CollectionId' => $collectionName
        ]);
        return true;
    }

    /*
     * to delete a collection
     */
    function deleteCollection(string $collectionName) {
        $this->rekognition->deleteCollection([
            'CollectionId' => $collectionName
        ]);
        return true;
    }

    /*
     * to detect details in the face
     */
    function detectFaces(string $fileimage) {
        return $result = $this->rekognition->detectFaces([
            'Image' => [ // REQUIRED
                'Bytes' => $this->imageread($fileimage)
            ],
        ]);
    }

    function detectFacess3(string $s3name, string $bucketname) {
        return $result = $this->rekognition->detectFaces([
            'Image' => [ // REQUIRED
                'S3Object' => [
                    'Bucket' => $bucketname,
                    'Name' => $s3name
                ]
            ],
        ]);
    }

    /*
     * to detect label details in the face
     */
    function detectLabelfromS3(string $bucketname, string $s3name) {
        return $result = $this->rekognition->DetectLabels([
            'Image' => [ // REQUIRED
                'S3Object' => [
                    'Bucket' => $bucketname,
                    'Name' => $s3name
                ]
            ],
        ]);
    }


    function detectLabel(string $fileimage) {
        return $result = $this->rekognition->DetectLabels([
            'Image' => [ // REQUIRED
                'Bytes' => $this->imageread($fileimage)
            ],
        ]);
    }

    /*
     * To save a face to image rekognition
     */
    function saveFacetoRekognition(string $collectionName, string $fileimage) {
        return $result = $this->rekognition->indexFaces([
            'CollectionId' => $collectionName, // REQUIRED
            'Image' => [ // REQUIRED
                'Bytes' => $this->imageread($fileimage)
            ],
            'QualityFilter' => 'AUTO',
        ]);
    }

    /*
     * To save a face to image rekognition
     */
    function indexFace(string $collectionName, string $fileimage, string $EID) {
        return $result = $this->rekognition->indexFaces([
            'CollectionId' => $collectionName, // REQUIRED
            'DetectionAttributes' => ['ALL'],
            'ExternalImageId' => $EID,
            'Image' => [ // REQUIRED
                'Bytes' => $this->imageread($fileimage)
            ],
            'QualityFilter' => 'AUTO',
        ]);
    }

    function indexFaces3(string $collectionName, string $s3name, string $bucketname, string $EID) {
        return $result = $this->rekognition->indexFaces([
            'CollectionId' => $collectionName, // REQUIRED
            'DetectionAttributes' => ['ALL'],
            'ExternalImageId' => $EID,
            'Image' => [ // REQUIRED
                'S3Object' => [
                    'Bucket' => $bucketname,
                    'Name' => $s3name
                ]
            ],
            'QualityFilter' => 'AUTO',
        ]);
    }


    function listCollectionRekognition() {
        return $result = $this->rekognition->listCollections();
    }

    /*
     * List all the faces in the collection
    */
    function listAllFaces(string $collectionName) {
        return $result = $this->rekognition->listFaces([
            'CollectionId' => $collectionName
        ]);
    }


    /*
    * delete a face using its id from the collection
    */
    function deleteFaces(string $collectionName, string $faceId) {
        return $result = $this->rekognition->deleteFaces([
            'CollectionId' => $collectionName,
            'FaceIds' => [
                $faceId,
            ],
        ]);
    }

    /*
     * Get image id from the given face
    */
    function getImageIdFromGivenFace(string $collectionName, string $fileimage) {

        return $result = $this->rekognition->searchFacesByImage([
            'CollectionId' => $collectionName, // REQUIRED
            'FaceMatchThreshold' => 90,
            'Image' => [ // REQUIRED
                'Bytes' => $this->imageread($fileimage)
            ]
        ]);
    }

    function getImageIdFromGivenFaceinS3(string $collectionName, string $s3name, string $bucketname) {
        try{
            $result = $this->rekognition->searchFacesByImage([
                'CollectionId' => $collectionName, // REQUIRED
                'FaceMatchThreshold' => 90,
                'Image' => [ // REQUIRED
                    'S3Object' => [
                        'Bucket' => $bucketname,
                        'Name' => $s3name
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return array('statusCode' => 500);
        }
       return $result;
    }

    function imageread($fileimage) {
        $fp_image = fopen($fileimage, 'r');
        $image = fread($fp_image, filesize($fileimage));
        fclose($fp_image);
        return $image;
    }

}

class awsface {
    function checkimage(string $mediapath) {
        if (@is_array(getimagesize($mediapath))) {
            return true;
        } else {
            return false;
        }
    }

    function facecount(string $imagepath) {
        $awsclass = new PHPAWS();
        return count($awsclass->detectFaces($imagepath)['FaceDetails']);
    }

    function facecounts3(string $imagename) {
        $awsclass = new PHPAWS();
        return count($awsclass->detectFacess3($imagename, 'paypeople')['FaceDetails']);
    }


    function facecounts3training(string $imagename) {
        $awsclass = new PHPAWS();
        return count($awsclass->detectFacess3($imagename, 'paypeopletrainedface')['FaceDetails']);
    }


    function faceexists(string $imagepath, string $companyid) {
        $awsclass = new PHPAWS();
        $awsclass->createorconnecttoCollection($companyid);
        return $awsclass->getImageIdFromGivenFace($companyid, $imagepath)['FaceMatches'];
    }

    function uploadtos3($imgpath, $ext) {
        $awsclass = new PHPAWS();
        return $awsclass->uploadimagetos3($imgpath, 'paypeople', $ext);
    }

    function uploadtos3facetraining($imgpath, $ext) {
        $awsclass = new PHPAWS();
        return $awsclass->uploadimagetos3($imgpath, 'paypeopletrainedface', $ext);
    }

    function faceexistsfroms3(string $s3name, string $companyid) {
        $awsclass = new PHPAWS();
        return $awsclass->getImageIdFromGivenFaceinS3($companyid, $s3name, 'paypeople')['FaceMatches'];
    }

    function faceexistsfroms3training(string $s3name, string $companyid) {
        $awsclass = new PHPAWS();
        return $awsclass->getImageIdFromGivenFaceinS3($companyid, $s3name, 'paypeopletrainedface')['FaceMatches'];
    }

    function indexfacefroms3(string $s3name, string $collectionName, string $EID) {
        $awsclass = new PHPAWS();
        return $awsclass->indexFaces3($collectionName, $s3name, 'paypeople', $EID);
    }

    function indexfacefroms3training(string $s3name, string $collectionName, string $EID) {
        $awsclass = new PHPAWS();
        return $awsclass->indexFaces3($collectionName, $s3name, 'paypeopletrainedface', $EID);
    }

    function trainedfacelist($collectionName) {
        $awsclass = new PHPAWS();
        return $awsclass->listAllFaces($collectionName);
    }

    function deletetrainedface(string $collectionName, string $faceid) {
        $awsclass = new PHPAWS();
        return $awsclass->deleteFaces($collectionName, $faceid);
    }

    function listCollectionInRekognition() {
        $awsclass = new PHPAWS();
        return $awsclass->listCollectionRekognition();
    }


    function createCollection(string $collectionName) {
        $awsclass = new PHPAWS();
        return $awsclass->createorconnecttoCollection($collectionName);
    }

    function deleteCollection(string $collectionName) {
        $awsclass = new PHPAWS();
        return $awsclass->deleteCollection($collectionName);
    }

    function getlabel(string $image) {
        $awsclass = new PHPAWS();
        return $awsclass->detectLabelfromS3('paypeople', $image);
    }

}